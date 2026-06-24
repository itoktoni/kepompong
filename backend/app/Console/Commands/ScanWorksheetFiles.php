<?php

namespace App\Console\Commands;

use App\Models\MasterWorksheet;
use App\Models\Worksheet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanWorksheetFiles extends Command
{
    protected $signature = 'worksheet:scan
        {--path= : Custom folder path to scan}
        {--dry-run : Show files without saving to database}
        {--sync : Also check database entries against files (mark missing as inactive)}';

    protected $description = 'Scan worksheet folder for files and sync with worksheets table';

    private array $folders = [];

    public function handle(): int
    {
        $customPath = $this->option('path');
        $dryRun = $this->option('dry-run');
        $sync = $this->option('sync');

        $this->folders = $customPath
            ? [$customPath]
            : [
                storage_path('app/worksheets'),
                storage_path('app/public/worksheets'),
            ];

        $allFiles = [];
        $extensions = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];

        foreach ($this->folders as $folder) {
            if (!File::isDirectory($folder)) {
                $this->warn("Folder not found: {$folder}");
                continue;
            }

            $this->info("Scanning: {$folder}");
            $files = File::files($folder);

            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, $extensions)) continue;
                if ($file->getFilename() === 'sample.pdf') continue;

                $allFiles[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'ext' => $ext,
                    'size' => $file->getSize(),
                ];
            }
        }

        if (empty($allFiles)) {
            $this->warn('No worksheet files found.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Found " . count($allFiles) . " file(s):");
        $this->newLine();

        $rows = [];
        foreach ($allFiles as $i => $f) {
            $key = $this->filenameToKey($f['filename']);
            $title = $this->keyToTitle($key);
            $exists = MasterWorksheet::where('worksheet_key', $key)->exists();
            $sizeKb = round($f['size'] / 1024, 1);

            $rows[] = [
                $i + 1,
                $f['filename'],
                $key,
                $title,
                $f['ext'],
                $sizeKb . ' KB',
                $exists ? '✅ exists' : '🆕 new',
            ];
        }

        $this->table(
            ['#', 'Filename', 'Key', 'Title', 'Ext', 'Size', 'Status'],
            $rows
        );

        if ($dryRun) {
            $this->info('Dry run — no changes made.');
            return self::SUCCESS;
        }

        $this->newLine();
        $created = 0;
        $updated = 0;
        $skipped = 0;

        MasterWorksheet::truncate();

        foreach ($allFiles as $f) {
            $key = $this->filenameToKey($f['filename']);
            $title = $this->keyToTitle($key);

            $existing = MasterWorksheet::where('worksheet_key', $key)->first();

            if ($existing) {
                $changes = [];
                if ($existing->worksheet_title !== $title) {
                    $changes['worksheet_title'] = $title;
                }
                if (!$existing->worksheet_active) {
                    $changes['worksheet_active'] = true;
                }

                if (!empty($changes)) {
                    $existing->update($changes);
                    $updated++;
                    $this->line("  Updated: <comment>{$key}</comment>");
                } else {
                    $skipped++;
                }
            } else {
                MasterWorksheet::create([
                    'worksheet_key' => $key,
                    'worksheet_icon' => '📝',
                    'worksheet_title' => $title,
                    'worksheet_desc' => "Worksheet: {$title}",
                    'worksheet_age' => '3-10',
                    'worksheet_age_label' => '3-10 thn',
                    'worksheet_ages' => [3, 4, 5, 6, 7, 8, 9, 10],
                    'worksheet_skills' => [],
                    'worksheet_agama' => null,
                    'worksheet_plans' => null,
                    'worksheet_bg' => '#E8F5E9',
                    'worksheet_icon_color' => '#2E7D32',
                    'worksheet_is_api' => false,
                    'worksheet_sort_order' => 99,
                    'worksheet_active' => true,
                ]);
                $created++;
                $this->line("  Created: <info>{$key}</info>");
            }
        }

        if ($sync) {
            $this->newLine();
            $this->info('Syncing: checking database entries against files...');

            $fileKeys = array_map(fn($f) => $this->filenameToKey($f['filename']), $allFiles);
            $dbWorksheets = MasterWorksheet::where('worksheet_active', true)->get();

            $deactivated = 0;
            foreach ($dbWorksheets as $db) {
                if (!in_array($db->worksheet_key, $fileKeys)) {
                    $db->update(['worksheet_active' => false]);
                    $deactivated++;
                    $this->line("  Deactivated: <error>{$db->worksheet_key}</error> (file not found)");
                }
            }

            $this->info("Deactivated: {$deactivated}");
        }

        $this->newLine();
        $this->info("Done! Created: {$created}, Updated: {$updated}, Skipped: {$skipped}");

        return self::SUCCESS;
    }

    private function filenameToKey(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(' ', '_', $name);
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
        return strtolower($name);
    }

    private function keyToTitle(string $key): string
    {
        $title = str_replace('_', ' ', $key);
        $title = preg_replace_callback('/(?:^|\s)([a-z])/', fn($m) => strtoupper($m[0]), $title);
        $title = preg_replace_callback('/-([a-z])/', fn($m) => '-' . strtoupper($m[1]), $title);
        return $title;
    }
}
