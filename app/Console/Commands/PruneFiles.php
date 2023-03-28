<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PruneFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prunes storage';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $files = Storage::disk('local')->allFiles();
        foreach ($files as $file) {
            if (
                !Str::contains($file, '.gitignore')
                && Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file))
            ) {
                Storage::disk('local')->delete($file);
            }
        }
    }
}
