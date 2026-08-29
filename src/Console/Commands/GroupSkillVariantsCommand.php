<?php

declare(strict_types=1);

namespace Sendportal\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Models\Skill;
use Sendportal\Base\Support\SkillName;

class GroupSkillVariantsCommand extends Command
{
    protected $signature = 'sp:skills:group
        {--workspace=1 : Workspace to clean up}
        {--apply : Write the changes; without this the command only reports}';

    protected $description = 'Group variant spellings of a skill under one parent and repair names broken by the old comma splitter';

    public function handle(): int
    {
        $workspaceId = (int) $this->option('workspace');
        $apply = (bool) $this->option('apply');

        $skills = Skill::withoutGlobalScopes()
            ->where('workspace_id', $workspaceId)
            ->where('parent_id', 0)
            ->get(['id', 'name', 'parent_id'])
            ->all();

        if (!$skills) {
            $this->error("No ungrouped skills in workspace {$workspaceId}.");

            return self::FAILURE;
        }

        $subscribers = DB::table('sendportal_skill_subscriber')
            ->select('skill_id', DB::raw('COUNT(*) AS total'))
            ->groupBy('skill_id')
            ->pluck('total', 'skill_id');

        $groups = [];
        foreach ($skills as $skill) {
            $groups[SkillName::groupKey($skill->name)][] = $skill;
        }

        [$grouped, $renamed] = [[], []];

        foreach ($groups as $members) {
            // An intact name makes the better parent; only fall back to reach when
            // every variant in the group is damaged.
            usort($members, static fn ($a, $b) => [
                SkillName::isWellFormed($b->name), $subscribers[$b->id] ?? 0, mb_strlen($a->name),
            ] <=> [
                SkillName::isWellFormed($a->name), $subscribers[$a->id] ?? 0, mb_strlen($b->name),
            ]);

            $parent = array_shift($members);
            $canonical = SkillName::canonical($parent->name);

            if ($canonical !== $parent->name) {
                $renamed[] = [$parent, $canonical];
            }

            foreach ($members as $child) {
                $grouped[] = [$child, $parent];
            }
        }

        $this->reportRenames($renamed);
        $this->reportGroups($grouped, $subscribers);

        $this->newLine();
        $this->line(sprintf(
            '%d skills → %d after grouping. %d names repaired, %d nested under a parent.',
            count($skills),
            count($groups),
            count($renamed),
            count($grouped)
        ));

        if (!$apply) {
            $this->warn('Dry run — nothing written. Re-run with --apply to commit.');

            return self::SUCCESS;
        }

        DB::transaction(static function () use ($renamed, $grouped) {
            foreach ($renamed as [$skill, $canonical]) {
                Skill::withoutGlobalScopes()->where('id', $skill->id)->update(['name' => $canonical]);
            }

            foreach ($grouped as [$child, $parent]) {
                Skill::withoutGlobalScopes()->where('id', $child->id)->update(['parent_id' => $parent->id]);
            }
        });

        $this->info('Applied. Subscribers are untouched — a parent now reports its whole group.');

        return self::SUCCESS;
    }

    private function reportRenames(array $renamed): void
    {
        if (!$renamed) {
            return;
        }

        $this->info('Names repaired');
        $this->table(['id', 'before', 'after'], array_map(
            static fn ($row) => [$row[0]->id, $row[0]->name, $row[1]],
            array_slice($renamed, 0, 20)
        ));

        if (count($renamed) > 20) {
            $this->line(sprintf('  … and %d more', count($renamed) - 20));
        }
    }

    private function reportGroups(array $grouped, $subscribers): void
    {
        if (!$grouped) {
            return;
        }

        $this->info('Nested under a parent');
        $this->table(['child', 'child subs', 'parent', 'parent subs'], array_map(
            static fn ($row) => [
                $row[0]->name,
                $subscribers[$row[0]->id] ?? 0,
                $row[1]->name,
                $subscribers[$row[1]->id] ?? 0,
            ],
            array_slice($grouped, 0, 25)
        ));

        if (count($grouped) > 25) {
            $this->line(sprintf('  … and %d more', count($grouped) - 25));
        }
    }
}
