<?php

declare(strict_types=1);

namespace Tests\Feature\Templates;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RedesignDefaultTemplatesMigrationTest extends TestCase
{
    public function test_default_templates_use_new_shell_and_multiblock_design(): void
    {
        // Migrations (including the multi-block design seed) run for the test DB.
        $tpl = DB::table('sendportal_templates')
            ->whereNull('workspace_id')->where('kind', 'transactional')->where('code', 'interviewed')
            ->first();

        $this->assertNotNull($tpl, 'interviewed default template should exist');
        // The 4px colored top border was removed from `content` by 2026_06_17_110000.
        $this->assertStringNotContainsString('border-top:4px solid', $tpl->content);
        $this->assertStringContainsString('{{ brand_header_html }}', $tpl->content);
        $this->assertStringContainsString('{{ brand_social_html }}', $tpl->content);
        $this->assertStringContainsString('{{ brand_name }}', $tpl->content);
        // body copy preserved
        $this->assertStringContainsString('Interview Scheduled', $tpl->content);

        // data_json is now a NATIVE multi-block Unlayer design (not a single
        // opaque html block) so the title/body/button are editable in the editor.
        $this->assertNotNull($tpl->data_json, 'data_json must hold a multi-block design');
        $design = json_decode((string) $tpl->data_json, true);
        $this->assertIsArray($design);
        $this->assertSame(16, $design['schemaVersion'] ?? null);
        $this->assertCount(4, $design['body']['rows'] ?? [], 'design should have header/title/body/footer rows');

        $types = [];
        foreach ($design['body']['rows'] as $row) {
            foreach ($row['columns'][0]['contents'] as $content) {
                $types[] = $content['type'];
            }
        }
        $this->assertContains('text', $types, 'title/body should be editable text blocks');
        $this->assertContains('button', $types, 'interviewed has a CTA button block');
        $this->assertContains('html', $types, 'branding stays in html blocks');
    }

    public function test_unmodified_workspace_copy_is_refreshed_but_customized_is_preserved(): void
    {
        // Arrange a pre-redesign-like state for the 'interviewed' code:
        $old = '<div>OLD interviewed default content</div>';
        DB::table('sendportal_templates')
            ->whereNull('workspace_id')->where('kind', 'transactional')->where('code', 'interviewed')
            ->update(['content' => $old, 'data_json' => '{"stale":1}']);

        // Unmodified copy: content identical to the (old) default.
        DB::table('sendportal_templates')->insert([
            'workspace_id' => 9991, 'code' => 'interviewed', 'kind' => 'transactional',
            'name' => 'copy-unmodified', 'content' => $old, 'data_json' => '{"stale":1}',
            'is_default' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        // Customized copy: content differs from the default.
        DB::table('sendportal_templates')->insert([
            'workspace_id' => 9992, 'code' => 'interviewed', 'kind' => 'transactional',
            'name' => 'copy-customized', 'content' => '<div>CUSTOM</div>', 'data_json' => null,
            'is_default' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Act: run the migration's up() against this state.
        require_once dirname(__DIR__, 3) . '/database/migrations/2026_06_17_100000_redesign_default_transactional_templates.php';
        (new \RedesignDefaultTransactionalTemplates())->up();

        // Assert.
        $global = DB::table('sendportal_templates')->whereNull('workspace_id')->where('code', 'interviewed')->first();
        $unmod  = DB::table('sendportal_templates')->where('workspace_id', 9991)->where('code', 'interviewed')->first();
        $custom = DB::table('sendportal_templates')->where('workspace_id', 9992)->where('code', 'interviewed')->first();

        $this->assertStringContainsString('{{ brand_header_html }}', $global->content);
        $this->assertSame($global->content, $unmod->content, 'unmodified copy should be refreshed to the new content');
        $this->assertNull($unmod->data_json, 'refreshed copy must have data_json nulled');
        $this->assertSame('<div>CUSTOM</div>', $custom->content, 'customized copy must be left untouched');
    }
}
