<?php

declare(strict_types=1);

namespace Tests\Feature\Templates;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RedesignDefaultTemplatesMigrationTest extends TestCase
{
    public function test_default_templates_use_new_shell_and_null_data_json(): void
    {
        // Migrations (including this redesign) run for the test DB.
        $tpl = DB::table('sendportal_templates')
            ->whereNull('workspace_id')->where('kind', 'transactional')->where('code', 'interviewed')
            ->first();

        $this->assertNotNull($tpl, 'interviewed default template should exist');
        $this->assertNull($tpl->data_json, 'data_json must be nulled so raw HTML is source of truth');
        // The 4px colored top border was removed by 2026_06_17_110000.
        $this->assertStringNotContainsString('border-top:4px solid', $tpl->content);
        $this->assertStringContainsString('{{ brand_header_html }}', $tpl->content);
        $this->assertStringContainsString('{{ brand_social_html }}', $tpl->content);
        $this->assertStringContainsString('{{ brand_name }}', $tpl->content);
        // body copy preserved
        $this->assertStringContainsString('Interview Scheduled', $tpl->content);
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
