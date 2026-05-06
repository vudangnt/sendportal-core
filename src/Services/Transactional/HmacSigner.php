<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Transactional;

class HmacSigner
{
    public function sign(string $payload, string $secret, int $timestamp): string
    {
        $data = $timestamp . '.' . $payload;
        $hash = hash_hmac('sha256', $data, $secret);
        return 'sha256=' . $hash;
    }

    public function verify(string $payload, string $secret, int $timestamp, string $signature): bool
    {
        $expected = $this->sign($payload, $secret, $timestamp);
        return hash_equals($expected, $signature);
    }
}
