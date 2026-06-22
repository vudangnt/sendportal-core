<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Templates;

use Sendportal\Base\Services\Templates\TransactionalTemplateDesignBuilder;
use Sendportal\Base\Services\Templates\TransactionalTemplateSeedData;
use Tests\TestCase;

class TransactionalTemplateDesignBuilderTest extends TestCase
{
    private function sample(bool $withButton = true): array
    {
        return [
            'name'    => 'Interview confirmation',
            'color'   => TransactionalTemplateSeedData::INDIGO,
            'subject' => 'Interview scheduled: {{ job_title }}',
            'title'   => 'Interview Scheduled',
            'body'    => [
                'Hi <strong>{{ candidate_name }}</strong>,',
                'Your interview for <strong>{{ job_title }}</strong> has been scheduled.',
            ],
            'button'  => $withButton ? ['text' => 'Join Interview', 'href' => '{{ interview_link }}'] : null,
        ];
    }

    public function test_builds_four_row_multiblock_design(): void
    {
        $design = TransactionalTemplateDesignBuilder::design($this->sample());

        $this->assertSame(16, $design['schemaVersion']);
        $rows = $design['body']['rows'];
        $this->assertCount(4, $rows, 'header / title / body / footer');

        $types = [];
        foreach ($rows as $row) {
            foreach ($row['columns'][0]['contents'] as $content) {
                $types[] = $content['type'];
            }
        }
        // header html, title text, body text, button, footer html
        $this->assertSame(['html', 'text', 'text', 'button', 'html'], $types);
    }

    public function test_branding_placeholders_live_in_html_blocks(): void
    {
        $design = TransactionalTemplateDesignBuilder::design($this->sample());

        $headerHtml = $design['body']['rows'][0]['columns'][0]['contents'][0]['values']['html'];
        $footerHtml = $design['body']['rows'][3]['columns'][0]['contents'][0]['values']['html'];

        $this->assertStringContainsString('{{ brand_header_html }}', $headerHtml);
        $this->assertStringContainsString('{{ brand_name }}', $footerHtml);
        $this->assertStringContainsString('{{ brand_contact_html }}', $footerHtml);
        $this->assertStringContainsString('{{ brand_social_html }}', $footerHtml);
    }

    public function test_title_and_body_are_editable_text_blocks(): void
    {
        $design = TransactionalTemplateDesignBuilder::design($this->sample());

        $titleText = $design['body']['rows'][1]['columns'][0]['contents'][0]['values']['text'];
        $bodyText  = $design['body']['rows'][2]['columns'][0]['contents'][0]['values']['text'];

        $this->assertStringContainsString('Interview Scheduled', $titleText);
        $this->assertStringContainsString('Interview confirmation', $titleText, 'badge label');
        $this->assertStringContainsString('{{ candidate_name }}', $bodyText);
    }

    public function test_button_href_is_preserved_as_merge_tag(): void
    {
        $design = TransactionalTemplateDesignBuilder::design($this->sample());
        $button = $design['body']['rows'][2]['columns'][0]['contents'][1];

        $this->assertSame('button', $button['type']);
        $this->assertSame('{{ interview_link }}', $button['values']['href']['values']['href']);
        $this->assertSame(TransactionalTemplateSeedData::INDIGO, $button['values']['buttonColors']['backgroundColor']);
    }

    public function test_design_without_button_has_no_button_block(): void
    {
        $design = TransactionalTemplateDesignBuilder::design($this->sample(false));

        $bodyContents = $design['body']['rows'][2]['columns'][0]['contents'];
        $this->assertCount(1, $bodyContents, 'only the body text block, no CTA');
        $this->assertSame(0, $design['counters']['u_content_button']);
    }

    public function test_designJson_returns_valid_json_for_every_seeded_template(): void
    {
        foreach (TransactionalTemplateSeedData::all() as $code => $tpl) {
            $json = TransactionalTemplateDesignBuilder::designJson($tpl);
            $decoded = json_decode($json, true);
            $this->assertIsArray($decoded, "design for '{$code}' must be valid JSON");
            $this->assertSame(16, $decoded['schemaVersion'], "schemaVersion for '{$code}'");
            $this->assertCount(4, $decoded['body']['rows'], "row count for '{$code}'");
        }
    }
}
