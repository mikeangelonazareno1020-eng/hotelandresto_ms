<?php

namespace App\Console\Commands;

use App\Models\ApiDevice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MakeApiDevice extends Command
{
    protected $signature = 'iot:make-device {name?} {--uid=}';
    protected $description = 'Create an API device with a generated API key (prints the key once).';

    public function handle(): int
    {
        $name = $this->argument('name') ?? 'Device '.now()->format('YmdHis');
        $uid = $this->option('uid') ?: Str::uuid()->toString();
        $plain = Str::random(48);
        $hash = Hash::make($plain);

        $device = ApiDevice::create([
            'name' => $name,
            'uid' => $uid,
            'api_key_hash' => $hash,
            'is_active' => true,
        ]);

        $this->info('Device created:');
        $this->line('  ID:   '.$device->id);
        $this->line('  Name: '.$device->name);
        $this->line('  UID:  '.$device->uid);
        $this->newLine();
        $this->warn('Store this API key securely (shown only once):');
        $this->line($plain);
        $this->newLine();
        $this->info('Use headers:');
        $this->line('  X-DEVICE-UID: '.$device->uid);
        $this->line('  X-API-KEY:    '.$plain);

        return self::SUCCESS;
    }
}

