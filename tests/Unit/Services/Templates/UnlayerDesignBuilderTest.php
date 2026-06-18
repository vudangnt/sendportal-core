<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Templates;

use Sendportal\Base\Services\Templates\UnlayerDesignBuilder;
use Tests\TestCase;

class UnlayerDesignBuilderTest extends TestCase
{
    public function test_wraps_html_in_a_single_html_block(): void
    {
        $html = '<div>Hi {{ candidate_name }}</div>';
        $design = UnlayerDesignBuilder::designFromHtml($html);

        $this->assertSame(16, $design['schemaVersion']);
        $block = $design['body']['rows'][0]['columns'][0]['contents'][0];
        $this->assertSame('html', $block['type']);
        $this->assertSame($html, $block['values']['html']);
    }

    public function test_fromHtml_returns_valid_json_preserving_html(): void
    {
        $html = '<p>x &amp; y · {{ brand_name }}</p>';
        $json = UnlayerDesignBuilder::fromHtml($html);
        $decoded = json_decode($json, true);

        $this->assertIsArray($decoded);
        $this->assertSame(
            $html,
            $decoded['body']['rows'][0]['columns'][0]['contents'][0]['values']['html']
        );
    }
}
