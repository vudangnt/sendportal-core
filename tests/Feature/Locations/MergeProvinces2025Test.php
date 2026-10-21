<?php

declare(strict_types=1);

namespace Tests\Feature\Locations;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Sendportal\Base\SendportalBaseServiceProvider;

/**
 * Chạy trên DB test RIÊNG (DB_DATABASE=sendportal_core_vn34_testing): setUp xoá sạch bảng rồi tự dựng
 * schema tối thiểu, nên KHÔNG được trỏ vào DB nào khác.
 */
class MergeProvinces2025Test extends TestCase
{
    private const WS = 1;

    protected function getPackageProviders($app): array
    {
        return [SendportalBaseServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame('sendportal_core_vn34_testing', DB::getDatabaseName(), 'Wrong test database');

        Schema::disableForeignKeyConstraints();
        Schema::dropAllTables();
        Schema::enableForeignKeyConstraints();

        Schema::create('sendportal_locations', function ($t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('code')->nullable();
            $t->string('type')->nullable();
            $t->integer('workspace_id')->default(0);
            $t->integer('parent_id')->default(0);
            $t->timestamps();
        });
        Schema::create('sendportal_subscribers', function ($t) {
            $t->id();
            $t->unsignedInteger('workspace_id')->default(0);
            $t->string('email');
            $t->unsignedBigInteger('location_id')->nullable();
            $t->timestamps();
            $t->foreign('location_id')->references('id')->on('sendportal_locations');
        });
        Schema::create('sendportal_location_subscriber', function ($t) {
            $t->id();
            $t->unsignedBigInteger('location_id');
            $t->unsignedBigInteger('subscriber_id');
            $t->timestamps();
            $t->foreign('location_id')->references('id')->on('sendportal_locations');
            $t->foreign('subscriber_id')->references('id')->on('sendportal_subscribers');
        });
        Schema::create('sendportal_campaign_location', function ($t) {
            $t->id();
            $t->unsignedBigInteger('campaign_id');
            $t->unsignedBigInteger('location_id');
            $t->timestamps();
        });
    }

    private function loc(string $name, int $ws = self::WS): int
    {
        return (int) DB::table('sendportal_locations')->insertGetId([
            'name' => $name, 'workspace_id' => $ws, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function sub(string $email, ?int $locationId = null): int
    {
        return (int) DB::table('sendportal_subscribers')->insertGetId([
            'workspace_id' => self::WS, 'email' => $email, 'location_id' => $locationId,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function link(int $loc, int $sub): void
    {
        DB::table('sendportal_location_subscriber')->insert([
            'location_id' => $loc, 'subscriber_id' => $sub, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return array<int, int> subscriber ids linked to the location, sorted */
    private function subsOf(int $loc): array
    {
        return DB::table('sendportal_location_subscriber')->where('location_id', $loc)
            ->orderBy('subscriber_id')->pluck('subscriber_id')->map(function ($v) {
                return (int) $v;
            })->all();
    }

    private function merge(array $opts = []): string
    {
        $code = Artisan::call('sp:locations:merge-2025', array_merge([
            '--workspace' => self::WS,
            '--map' => __DIR__ . '/../../fixtures/provinces-2025.json',
        ], $opts));
        $this->lastExit = $code;

        return Artisan::output();
    }

    /** @var int */
    private $lastExit = 0;

    /** @return array<string, int> */
    private function hcmFixture(): array
    {
        $ids = [
            'hcm' => $this->loc('Hồ Chí Minh'),
            'bd' => $this->loc('Bình Dương'),
            'vt' => $this->loc('Bà Rịa - Vũng Tàu'),
            'vtAlias' => $this->loc('Vũng tàu'),
            'variant' => $this->loc('TP. Hồ Chí Minh'),
            'remote' => $this->loc('Remote'),
            'hn2' => $this->loc('Hà Nội2'),
        ];
        $s1 = $this->sub('a@x.vn', $ids['bd']);
        $s2 = $this->sub('b@x.vn', $ids['bd']);
        $s3 = $this->sub('c@x.vn');
        $s4 = $this->sub('d@x.vn');
        $s5 = $this->sub('e@x.vn');
        $this->link($ids['hcm'], $s1);   // s1 already on the kept row
        $this->link($ids['bd'], $s1);    // duplicate, must not be doubled
        $this->link($ids['bd'], $s2);
        $this->link($ids['vt'], $s3);
        $this->link($ids['variant'], $s4);
        $this->link($ids['remote'], $s5);
        DB::table('sendportal_campaign_location')->insert([
            'campaign_id' => 9, 'location_id' => $ids['bd'], 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $ids + ['s1' => $s1, 's2' => $s2, 's3' => $s3, 's4' => $s4, 's5' => $s5];
    }

    /** Plain-array copy of a table (stdClass identity would defeat assertSame). */
    private function rows(string $table): array
    {
        return json_decode(json_encode(DB::table($table)->orderBy('id')->get()), true);
    }

    private function snapshot(): array
    {
        $tables = ['sendportal_locations', 'sendportal_location_subscriber', 'sendportal_subscribers', 'sendportal_campaign_location'];

        return array_map(function ($t) {
            return $this->rows($t);
        }, $tables);
    }

    /** @test */
    public function it_requires_workspace_and_map(): void
    {
        $this->assertSame(1, Artisan::call('sp:locations:merge-2025', ['--map' => 'x.json']));
        $this->assertSame(1, Artisan::call('sp:locations:merge-2025', ['--workspace' => 1]));
        $this->assertSame(1, Artisan::call('sp:locations:merge-2025', ['--workspace' => 1, '--map' => '/nope.json']));
    }

    /** @test */
    public function dry_run_writes_nothing_and_reports_the_plan(): void
    {
        $ids = $this->hcmFixture();
        $before = $this->snapshot();

        $out = $this->merge();

        $this->assertSame(0, $this->lastExit);
        $this->assertSame($before, $this->snapshot());
        $this->assertSame([], DB::select("SHOW TABLES LIKE 'sendportal_locations_bak_%'"));
        $this->assertStringContainsString('DRY-RUN', $out);
        $this->assertStringContainsString('Bình Dương', $out);
        $this->assertStringContainsString('TP. Hồ Chí Minh', $out);
        $this->assertStringContainsString('biến thể giữ nguyên', $out);
        $this->assertStringContainsString('Remote', $out);
        $this->assertStringContainsString('chưa nhận ra', $out);
        $this->assertStringContainsString('pivot cần chuyển: 2', $out);       // s2 (bd) + s3 (vt)
        $this->assertStringContainsString('pivot trùng sẽ bỏ: 1', $out);      // s1
        $this->assertStringContainsString('subscribers.location_id cần đổi: 2', $out);
        $this->assertStringContainsString('campaign_location tham chiếu: 1', $out);
    }

    /** @test */
    public function apply_moves_pivots_without_duplicating_and_keeps_old_rows(): void
    {
        $ids = $this->hcmFixture();
        $campaignBefore = $this->rows('sendportal_campaign_location');

        $this->merge(['--apply' => true]);

        $this->assertSame([$ids['s1'], $ids['s2'], $ids['s3']], $this->subsOf($ids['hcm']));
        $this->assertSame([], $this->subsOf($ids['bd']));
        $this->assertSame([], $this->subsOf($ids['vt']));
        $this->assertSame([$ids['s4']], $this->subsOf($ids['variant']));
        $this->assertSame([$ids['s5']], $this->subsOf($ids['remote']));
        $this->assertSame(7, DB::table('sendportal_locations')->count(), 'old rows are kept');
        $this->assertSame($ids['hcm'], (int) DB::table('sendportal_subscribers')->where('id', $ids['s1'])->value('location_id'));
        $this->assertSame($ids['hcm'], (int) DB::table('sendportal_subscribers')->where('id', $ids['s2'])->value('location_id'));
        $this->assertSame($campaignBefore, $this->rows('sendportal_campaign_location'));
        $this->assertSame('TP. Hồ Chí Minh', DB::table('sendportal_locations')->where('id', $ids['variant'])->value('name'));
        $this->assertSame('Remote', DB::table('sendportal_locations')->where('id', $ids['remote'])->value('name'));
    }

    /** @test */
    public function apply_is_idempotent(): void
    {
        $this->hcmFixture();
        $this->merge(['--apply' => true]);
        $after = $this->snapshot();

        $out = $this->merge(['--apply' => true]);

        $this->assertSame($after, $this->snapshot());
        $this->assertStringContainsString('Không có gì để thay đổi', $out);
    }

    /** @test */
    public function thua_thien_hue_merges_into_existing_hue(): void
    {
        $old = $this->loc('Thừa Thiên Huế');
        $hue = $this->loc('Huế');
        $a = $this->sub('a@x.vn');
        $b = $this->sub('b@x.vn');
        $this->link($old, $a);
        $this->link($old, $b);
        $this->link($hue, $b);

        $this->merge(['--apply' => true]);

        $this->assertSame([$a, $b], $this->subsOf($hue));
        $this->assertSame([], $this->subsOf($old));
        $this->assertSame('Thừa Thiên Huế', DB::table('sendportal_locations')->where('id', $old)->value('name'));
        $this->assertSame('Huế', DB::table('sendportal_locations')->where('id', $hue)->value('name'));
    }

    /** @test */
    public function the_old_first_name_row_is_renamed_when_no_row_has_the_new_name(): void
    {
        $old = $this->loc('Thừa Thiên Huế');
        $a = $this->sub('a@x.vn');
        $this->link($old, $a);

        $out = $this->merge();
        $this->assertSame('Thừa Thiên Huế', DB::table('sendportal_locations')->where('id', $old)->value('name'));
        $this->assertStringContainsString('Đổi tên', $out);

        $this->merge(['--apply' => true]);

        $this->assertSame('Huế', DB::table('sendportal_locations')->where('id', $old)->value('name'));
        $this->assertSame([$a], $this->subsOf($old));
    }

    /** @test */
    public function alias_rows_merge_into_their_province_row(): void
    {
        $kh = $this->loc('Khánh Hòa');
        $nt = $this->loc('Nha Trang');
        $dl = $this->loc('Đà Lạt');
        $ld = $this->loc('Lâm Đồng');
        $a = $this->sub('a@x.vn');
        $b = $this->sub('b@x.vn');
        $this->link($nt, $a);
        $this->link($dl, $b);

        $this->merge(['--apply' => true]);

        $this->assertSame([$a], $this->subsOf($kh));
        $this->assertSame([$b], $this->subsOf($ld));
    }

    /** @test */
    public function merged_rows_without_a_kept_row_are_skipped_and_reported(): void
    {
        $bd = $this->loc('Bình Dương');
        $a = $this->sub('a@x.vn');
        $this->link($bd, $a);

        $out = $this->merge(['--apply' => true]);

        $this->assertSame([$a], $this->subsOf($bd));
        $this->assertStringContainsString('thiếu dòng giữ', $out);
    }

    /** @test */
    public function other_workspaces_are_not_touched(): void
    {
        $hcm = $this->loc('Hồ Chí Minh');
        $bd2 = $this->loc('Bình Dương', 2);
        $a = $this->sub('a@x.vn');
        $this->link($bd2, $a);

        $this->merge(['--apply' => true]);

        $this->assertSame([$a], $this->subsOf($bd2));
        $this->assertSame([], $this->subsOf($hcm));
    }

    /** @test */
    public function restore_returns_original_pivots_names_and_subscriber_locations(): void
    {
        $ids = $this->hcmFixture();
        $old = $this->loc('Thừa Thiên Huế');
        $c = $this->sub('z@x.vn', $old);
        $this->link($old, $c);
        $before = $this->snapshot();

        $this->merge(['--apply' => true]);
        $this->assertNotSame($before, $this->snapshot());

        $out = $this->merge(['--restore' => date('Ymd')]);

        $this->assertSame(0, $this->lastExit, $out);
        $this->assertSame([$ids['s1']], $this->subsOf($ids['hcm']));
        $this->assertSame([$ids['s1'], $ids['s2']], $this->subsOf($ids['bd']));
        $this->assertSame([$ids['s3']], $this->subsOf($ids['vt']));
        $this->assertSame('Thừa Thiên Huế', DB::table('sendportal_locations')->where('id', $old)->value('name'));
        $this->assertSame($before[1], $this->rows('sendportal_location_subscriber'));
        $this->assertSame($before[2], $this->rows('sendportal_subscribers'));
        $this->assertSame($before[0], $this->rows('sendportal_locations'));
    }

    /** @test */
    public function restore_fails_cleanly_when_backup_is_missing(): void
    {
        $this->hcmFixture();

        $this->merge(['--restore' => '19990101']);

        $this->assertSame(1, $this->lastExit);
    }
}
