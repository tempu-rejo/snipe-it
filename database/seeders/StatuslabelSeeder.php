<?php

namespace Database\Seeders;

use App\Models\Statuslabel;
use App\Models\User;
use Illuminate\Database\Seeder;

class StatuslabelSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('permissions->superuser', '1')->first() ?? User::factory()->firstAdmin()->create();

        $statuses = [
            ['name' => 'Ready to Deploy', 'factory' => 'rtd'],
            ['name' => 'Pending', 'factory' => 'pending'],
            ['name' => 'Archived', 'factory' => 'archived'],
            ['name' => 'Out for Diagnostics', 'factory' => 'outForDiagnostics'],
            ['name' => 'Out for Repair', 'factory' => 'outForRepair'],
            ['name' => 'Broken - Not Fixable', 'factory' => 'broken'],
            ['name' => 'Lost/Stolen', 'factory' => 'lost'],
        ];

        foreach ($statuses as $status) {
            if (Statuslabel::query()->where('name', $status['name'])->exists()) {
                continue;
            }

            $factory = Statuslabel::factory();
            $factory->{$status['factory']}()->create([
                'name' => $status['name'],
                'created_by' => $admin->id,
            ]);
        }
    }
}
