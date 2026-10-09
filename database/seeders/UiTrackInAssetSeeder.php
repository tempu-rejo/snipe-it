<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\Statuslabel;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UiTrackInAssetSeeder extends Seeder
{
    private const MAX_ASSETS_PER_CATEGORY = 10;

    public function run(): void
    {
        $path = database_path('seeders/data/uitrackin_assets.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Unable to open asset seed data: {$path}");
        }

        try {
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new RuntimeException("Asset seed data is empty: {$path}");
            }

            $headers = array_map('trim', $headers);
            $counts = [];
            $admin = User::where('permissions->superuser', '1')->first()
                ?? User::factory()->firstAdmin()->create();

            while (($values = fgetcsv($handle)) !== false) {
                if (count($values) !== count($headers)) {
                    throw new RuntimeException('Asset seed CSV contains a row with an invalid number of columns.');
                }

                $row = array_combine($headers, array_map('trim', $values));
                $categoryName = $row['Category'] ?? '';
                $assetTag = $row['Asset Tag'] ?? '';
                $modelName = $row['Model'] ?? '';
                $manufacturerName = $row['Manufacturer'] ?? '';

                if ($categoryName === '' || $assetTag === '' || $modelName === '' || $manufacturerName === '') {
                    throw new RuntimeException('Asset seed CSV is missing a required category, asset tag, model, or manufacturer.');
                }

                $counts[$categoryName] = ($counts[$categoryName] ?? 0) + 1;
                if ($counts[$categoryName] > self::MAX_ASSETS_PER_CATEGORY) {
                    throw new RuntimeException("Asset seed data exceeds the limit of 10 assets for category '{$categoryName}'.");
                }

                if (Asset::withTrashed()->where('asset_tag', $assetTag)->exists()) {
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['name' => $categoryName, 'category_type' => 'asset'],
                    ['created_by' => $admin->id]
                );
                $manufacturer = Manufacturer::firstOrCreate(['name' => $manufacturerName]);
                $location = Location::firstOrCreate(['name' => $row['Location']]);
                $status = $this->findOrCreateStatus($row['Status'], $admin);
                $model = $this->findOrCreateModel(
                    $modelName,
                    $row['Model Number'] ?? '',
                    $category,
                    $manufacturer,
                    $admin
                );

                $asset = new Asset();
                $asset->forceFill([
                    'asset_tag' => $assetTag,
                    'name' => ($row['Name'] ?? '') !== '' ? $row['Name'] : $assetTag,
                    'model_id' => $model->id,
                    'serial' => ($row['Serial'] ?? '') !== '' ? $row['Serial'] : null,
                    'status_id' => $status->id,
                    'rtd_location_id' => $location->id,
                    'purchase_date' => ($row['Purchase Date'] ?? '') !== '' ? $row['Purchase Date'] : null,
                    'notes' => $this->hardwareNotes($row),
                    'created_by' => $admin->id,
                ]);

                if (! $asset->save()) {
                    throw new RuntimeException(
                        "Unable to create asset '{$assetTag}': ".$asset->getErrors()->toJson()
                    );
                }
            }
        } finally {
            fclose($handle);
        }
    }

    private function findOrCreateStatus(string $name, User $admin): Statuslabel
    {
        $status = Statuslabel::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if ($status) {
            return $status;
        }

        $status = new Statuslabel();
        $status->forceFill([
            'name' => $name,
            'notes' => '',
            'deployable' => 1,
            'pending' => 0,
            'archived' => 0,
            'default_label' => 1,
            'created_by' => $admin->id,
        ]);
        if (! $status->save()) {
            throw new RuntimeException("Unable to create status '{$name}': ".$status->getErrors()->toJson());
        }

        return $status;
    }

    private function findOrCreateModel(
        string $name,
        string $modelNumber,
        Category $category,
        Manufacturer $manufacturer,
        User $admin
    ): AssetModel {
        $model = AssetModel::query()
            ->where('name', $name)
            ->where('category_id', $category->id)
            ->first();

        if ($model) {
            return $model;
        }

        $model = new AssetModel();
        $model->forceFill([
            'name' => $name,
            'model_number' => $modelNumber !== '' && $modelNumber !== '-' ? $modelNumber : null,
            'category_id' => $category->id,
            'manufacturer_id' => $manufacturer->id,
            'created_by' => $admin->id,
        ]);
        if (! $model->save()) {
            throw new RuntimeException("Unable to create model '{$name}': ".$model->getErrors()->toJson());
        }

        return $model;
    }

    private function hardwareNotes(array $row): ?string
    {
        $notes = [];
        foreach ([
            'CPU' => 'CPU',
            'RAM' => 'RAM',
            'Storage' => 'Storage',
            'System Type' => 'System type',
            'Device Type' => 'Device type',
        ] as $field => $label) {
            if (($row[$field] ?? '') !== '') {
                $notes[] = "{$label}: {$row[$field]}";
            }
        }

        return $notes === [] ? null : implode("\n", $notes);
    }
}
