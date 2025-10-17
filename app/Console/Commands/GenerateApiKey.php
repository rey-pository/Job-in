<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiKey;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    protected $signature = 'app:generate-api-key {name}';
    protected $description = 'Generate a new API key for a third-party application';

    public function handle()
    {
        $name = $this->argument('name');
        $key = Str::random(40);

        ApiKey::create([
            'name' => $name,
            'key' => hash('sha256', $key), 
        ]);

        $this->info("API Key for '{$name}' created successfully.");
        $this->warn("Here is the key (simpan baik-baik, hanya akan ditampilkan sekali):");
        $this->line($key);
        
        return 0;
    }
}