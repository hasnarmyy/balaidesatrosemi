<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SipegSqlSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sipeg.sql');
        if (!file_exists($path)) {
            $this->command->error("File database/sipeg.sql not found.");
            return;
        }

        $sql = file_get_contents($path);

        // Extract all INSERT statements
        preg_match_all('/INSERT INTO .*?;\n/s', $sql, $matches);

        if (empty($matches[0])) {
            $this->command->info('No INSERT statements found in sipeg.sql');
            return;
        }

        // Skip inserts for tables we don't want to import (migrations, cache, jobs, sessions, etc.)
        $exclude = [
            'migrations', 'cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'sessions'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($matches[0] as $insertStmt) {
            $stmt = trim($insertStmt);
            if (preg_match('/INSERT INTO `?(\w+)`?/i', $stmt, $m)) {
                $table = $m[1];
                if (in_array(strtolower($table), $exclude)) {
                    continue;
                }
            }

            try {
                DB::unprepared($stmt);
            } catch (\Exception $e) {
                $this->command->error('Failed to run statement for table: ' . ($table ?? 'unknown'));
                $this->command->error($e->getMessage());
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Imported INSERT statements from sipeg.sql');
    }
}
