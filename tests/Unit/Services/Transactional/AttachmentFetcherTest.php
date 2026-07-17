<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Transactional;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Sendportal\Base\Services\Transactional\AttachmentFetcher;
use Tests\TestCase;

class AttachmentFetcherTest extends TestCase
{
    private function fetcherReturning(string $body, array $headers = []): AttachmentFetcher
    {
        $mock = new MockHandler([
            new Response(200, $headers + ['Content-Type' => 'application/pdf'], Utils::streamFor($body)),
        ]);

        return new AttachmentFetcher(new Client(['handler' => HandlerStack::create($mock)]));
    }

    public function test_it_blocks_non_http_schemes(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('only http/https');

        $this->fetcherReturning('x')->fetch([['url' => 'file:///etc/passwd']]);
    }

    public function test_it_blocks_loopback_addresses(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('non-public address');

        $this->fetcherReturning('x')->fetch([['url' => 'http://127.0.0.1/secret.pdf']]);
    }

    public function test_it_blocks_ipv6_loopback(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('non-public address');

        $this->fetcherReturning('x')->fetch([['url' => 'http://[::1]/secret.pdf']]);
    }

    public function test_it_blocks_cloud_metadata_endpoint(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('non-public address');

        $this->fetcherReturning('x')->fetch([['url' => 'http://169.254.169.254/latest/meta-data/']]);
    }

    public function test_it_blocks_private_range(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('non-public address');

        $this->fetcherReturning('x')->fetch([['url' => 'http://10.0.0.5/internal.pdf']]);
    }

    public function test_it_rejects_too_many_attachments(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Too many attachments');

        $items = array_fill(0, AttachmentFetcher::MAX_COUNT + 1, ['url' => 'https://example.com/a.pdf']);
        $this->fetcherReturning('x')->fetch($items);
    }

    public function test_it_aborts_when_a_file_exceeds_the_per_file_limit(): void
    {
        Storage::fake();

        $oversize = str_repeat('a', AttachmentFetcher::MAX_FILE_BYTES + 1024);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('per-file limit');

        $this->fetcherReturning($oversize)->fetch([['url' => 'https://example.com/big.pdf']]);
    }

    public function test_it_stores_the_file_and_derives_a_safe_filename(): void
    {
        Storage::fake();

        $result = $this->fetcherReturning('PDFDATA')->fetch([
            ['url' => 'https://example.com/files/offer.pdf'],
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('offer.pdf', $result[0]['filename']);
        $this->assertSame('application/pdf', $result[0]['content_type']);
        $this->assertSame(7, $result[0]['size']);
        Storage::assertExists($result[0]['path']);
    }

    public function test_it_sanitises_path_traversal_in_filename(): void
    {
        Storage::fake();

        $result = $this->fetcherReturning('DATA')->fetch([
            ['url' => 'https://example.com/a.pdf', 'filename' => '../../etc/passwd'],
        ]);

        $this->assertSame('passwd', $result[0]['filename']);
    }

    public function test_cleanup_removes_stored_files(): void
    {
        Storage::fake();

        $fetcher = $this->fetcherReturning('DATA');
        $result = $fetcher->fetch([['url' => 'https://example.com/a.pdf']]);
        Storage::assertExists($result[0]['path']);

        $fetcher->cleanup(array_column($result, 'path'));
        Storage::assertMissing($result[0]['path']);
    }
}
