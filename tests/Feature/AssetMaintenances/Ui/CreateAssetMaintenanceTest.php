<?php

namespace Tests\Feature\AssetMaintenances\Ui;

use App\Models\Asset;
use App\Models\Statuslabel;
use App\Models\Supplier;
use App\Models\User;
use Tests\TestCase;

class CreateAssetMaintenanceTest extends TestCase
{
    public function testPageRenders()
    {
        $this->actingAs(User::factory()->superuser()->create())
            ->get(route('maintenances.create'))
            ->assertOk();
    }

    public function testCanCreateAssetMaintenance()
    {
        $actor = User::factory()->superuser()->create();

        $asset = Asset::factory()->create();
        $supplier = Supplier::factory()->create();
        $repairStatus = Statuslabel::factory()->create(['name' => 'IN Repair – IT']);

        $this->actingAs($actor)
            ->followingRedirects()
            ->post(route('maintenances.store'), [
                'title' => 'Test Maintenance',
                'asset_id' => $asset->id,
                'supplier_id' => $supplier->id,
                'asset_maintenance_type' => $repairStatus->name,
                'start_date' => '2021-01-01',
                'completion_date' => '2021-01-10',
                'is_warranty' => '1',
                'cost' => '100.00',
                'notes' => 'A note',
            ])
            ->assertOk();

        $this->assertDatabaseHas('asset_maintenances', [
            'asset_id' => $asset->id,
            'supplier_id' => $supplier->id,
            'asset_maintenance_type' => $repairStatus->name,
            'title' => 'Test Maintenance',
            'is_warranty' => 1,
            'start_date' => '2021-01-01',
            'completion_date' => '2021-01-10',
            'asset_maintenance_time' => '9',
            'notes' => 'A note',
            'cost' => '100.00',
            'created_by' => $actor->id,
        ]);

        $this->assertSame($repairStatus->id, $asset->fresh()->status_id);
    }

    public function testRepairMaintenanceTypeSetsMatchingAssetStatus()
    {
        $actor = User::factory()->superuser()->create();
        $supplier = Supplier::factory()->create();

        foreach (['IN Repair – IT', 'IN Repair – Vendor'] as $repairType) {
            $asset = Asset::factory()->create();
            $repairStatus = Statuslabel::factory()->create(['name' => $repairType]);

            $this->actingAs($actor)
                ->followingRedirects()
                ->post(route('maintenances.store'), [
                    'title' => 'Repair '.$repairType,
                    'asset_id' => $asset->id,
                    'supplier_id' => $supplier->id,
                    'asset_maintenance_type' => $repairType,
                    'start_date' => '2021-01-01',
                ])
                ->assertOk();

            $this->assertDatabaseHas('assets', [
                'id' => $asset->id,
                'status_id' => $repairStatus->id,
            ]);
        }
    }
}
