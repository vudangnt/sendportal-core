<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Downloads transactional email attachments from caller-supplied public URLs.
 *
 * Fetching an arbitrary URL server-side is an SSRF sink, so every request is
 * constrained: http(s) only, the resolved IP must be public (blocks loopback,
 * RFC1918, link-local incl. the cloud metadata endpoint, ULA...), redirects are
 * capped and re-validated per hop, and the body is streamed with a hard byte
 * cap (Content-Length is never trusted).
 *
 * Size policy (approved): each file < 25 MB and the total <= 25 MB, because
 * receivers such as Gmail/Outlook reject messages larger than ~25 MB even
 * though SES v2 itself accepts up to 40 MB after MIME encoding.
 */
class AttachmentFetcher
{
    public const MAX_FILE_BYTES = 25 * 1024 * 1024;
    public const MAX_TOTAL_BYTES = 25 * 1024 * 1024;
    public const MAX_COUNT = 10;

    private const MAX_REDIRECTS = 3;
    private const CONNECT_TIMEOUT = 10;
    private const TOTAL_TIMEOUT = 60;
    private const DISK_DIR = 'transactional-attachments';

    private ?Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * Download + validate every attachment and persist the bytes to storage.
     *
     * @param array<int, array{url: string, filename?: string|null}> $attachments
     * @return array<int, array{filename: string, content_type: string, path: string, size: int}>
     *
     * @throws RuntimeException on any validation/download failure (caller maps to 422)
     */
    public function fetch(array $attachments): array
    {
        if (count($attachments) > self::MAX_COUNT) {
            throw new RuntimeException(sprintf(
                'Too many attachments: %d (max %d).',
                count($attachments),
                self::MAX_COUNT
            ));
        }

        $stored = [];
        $totalBytes = 0;
        $batch = Str::uuid()->toString();

        try {
            foreach ($attachments as $i => $attachment) {
                $url = (string) ($attachment['url'] ?? '');
                $remaining = self::MAX_TOTAL_BYTES - $totalBytes;

                $file = $this->download($url, $remaining, $i);

                $filename = $this->resolveFilename(
                    $attachment['filename'] ?? null,
                    $url,
                    $file['content_disposition']
                );

                $path = self::DISK_DIR . '/' . $batch . '/' . Str::uuid()->toString();
                Storage::put($path, $file['body']);

                $totalBytes += $file['size'];
                $stored[] = [
                    'filename' => $filename,
                    'content_type' => $file['content_type'],
                    'path' => $path,
                    'size' => $file['size'],
                ];
            }
        } catch (\Throwable $e) {
            // Never leave orphaned bytes behind when the batch fails.
            $this->cleanup(array_column($stored, 'path'));
            throw $e;
        }

        return $stored;
    }

    /** Delete stored attachment files (called after the message is relayed). */
    public function cleanup(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
        }
    }

    /**
     * @return array{body: string, size: int, content_type: string, content_disposition: ?string}
     */
    private function download(string $url, int $remainingTotal, int $index): array
    {
        $uri = $this->validateUrl($url, $index);

        $response = $this->client()->request('GET', (string) $uri, [
            RequestOptions::STREAM => true,
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
            RequestOptions::TIMEOUT => self::TOTAL_TIMEOUT,
            RequestOptions::ALLOW_REDIRECTS => [
                'max' => self::MAX_REDIRECTS,
                'strict' => true,
                'referer' => false,
                'protocols' => ['http', 'https'],
                // Re-validate every hop: a public URL can 302 to an internal one.
                'on_redirect' => function ($request, $response, UriInterface $to) use ($index) {
                    $this->assertPublicUri($to, $index);
                },
            ],
        ]);

        $body = $response->getBody();
        $limit = min(self::MAX_FILE_BYTES, max(0, $remainingTotal));

        $buffer = '';
        $size = 0;
        // Stream with a hard cap — Content-Length may be absent or lie.
        while (!$body->eof()) {
            $chunk = $body->read(262144);
            if ($chunk === '') {
                break;
            }
            $size += strlen($chunk);

            if ($size > self::MAX_FILE_BYTES) {
                throw new RuntimeException(sprintf(
                    'attachments.%d: file exceeds the %d MB per-file limit.',
                    $index,
                    self::MAX_FILE_BYTES / 1024 / 1024
                ));
            }
            if ($size > $limit) {
                throw new RuntimeException(sprintf(
                    'attachments.%d: attachments exceed the %d MB total limit.',
                    $index,
                    self::MAX_TOTAL_BYTES / 1024 / 1024
                ));
            }

            $buffer .= $chunk;
        }

        if ($size === 0) {
            throw new RuntimeException(sprintf('attachments.%d: downloaded file is empty.', $index));
        }

        return [
            'body' => $buffer,
            'size' => $size,
            'content_type' => $this->headerValue($response->getHeaderLine('Content-Type'))
                ?: 'application/octet-stream',
            'content_disposition' => $response->getHeaderLine('Content-Disposition') ?: null,
        ];
    }

    /** Validate scheme + that the host resolves to a public IP. */
    private function validateUrl(string $url, int $index): UriInterface
    {
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException(sprintf('attachments.%d: invalid url.', $index));
        }

        $uri = new Uri($url);
        $this->assertPublicUri($uri, $index);

        return $uri;
    }

    private function assertPublicUri(UriInterface $uri, int $index): void
    {
        $scheme = strtolower($uri->getScheme());
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new RuntimeException(sprintf(
                'attachments.%d: only http/https urls are allowed (got "%s").',
                $index,
                $scheme
            ));
        }

        // An IPv6 literal host arrives bracketed ("[::1]"); strip them so the
        // literal is validated as an IP rather than falling through to DNS.
        $host = trim($uri->getHost(), '[]');
        if ($host === '') {
            throw new RuntimeException(sprintf('attachments.%d: url has no host.', $index));
        }

        foreach ($this->resolveIps($host, $index) as $ip) {
            if (!$this->isPublicIp($ip)) {
                throw new RuntimeException(sprintf(
                    'attachments.%d: url resolves to a non-public address (%s) and was blocked.',
                    $index,
                    $ip
                ));
            }
        }
    }

    /** @return array<int, string> */
    private function resolveIps(string $host, int $index): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $ips = [];
        foreach (['A' => DNS_A, 'AAAA' => DNS_AAAA] as $key => $type) {
            $records = @dns_get_record($host, $type) ?: [];
            foreach ($records as $record) {
                $ip = $record['ip'] ?? $record['ipv6'] ?? null;
                if ($ip) {
                    $ips[] = $ip;
                }
            }
        }

        if ($ips === []) {
            throw new RuntimeException(sprintf('attachments.%d: could not resolve host "%s".', $index, $host));
        }

        return $ips;
    }

    private function isPublicIp(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /** Derive a safe filename: explicit > Content-Disposition > URL path > fallback. */
    private function resolveFilename(?string $explicit, string $url, ?string $contentDisposition): string
    {
        $name = $explicit;

        if (!$name && $contentDisposition && preg_match('/filename\*?=(?:UTF-8\'\')?"?([^";]+)"?/i', $contentDisposition, $m)) {
            $name = urldecode($m[1]);
        }

        if (!$name) {
            $name = basename(parse_url($url, PHP_URL_PATH) ?: '');
        }

        // Strip any path components / control chars a caller or header could inject.
        $name = basename(str_replace('\\', '/', (string) $name));
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? '';
        $name = trim($name);

        if ($name === '' || $name === '.' || $name === '..') {
            $name = 'attachment';
        }

        return Str::limit($name, 200, '');
    }

    private function headerValue(string $header): string
    {
        return trim(explode(';', $header)[0] ?? '');
    }

    private function client(): Client
    {
        return $this->client ??= new Client();
    }
}
