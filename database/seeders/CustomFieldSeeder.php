<?php

namespace Database\Seeders;

use App\Models\CustomField;
use App\Models\CustomFieldset;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomFieldSeeder extends Seeder
{
    public function run()
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('assets');

        foreach ($columns as $column) {
            if (strpos($column, '_snipeit_') !== false) {
                Schema::table('assets', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
        CustomField::truncate();
        CustomFieldset::truncate();
        DB::table('custom_field_custom_fieldset')->truncate();

        CustomFieldset::factory()->count(1)->mobile()->create();
        CustomFieldset::factory()->count(1)->computer()->create();
        $assetDetailFieldset = CustomFieldset::factory()->create(['name' => 'Asset Detail']);
        CustomField::factory()->count(1)->imei()->create();
        CustomField::factory()->count(1)->phone()->create();
        CustomField::factory()->count(1)->ram()->create();
        CustomField::factory()->count(1)->cpu()->create();
        CustomField::factory()->count(1)->macAddress()->create();
        CustomField::factory()->count(1)->testEncrypted()->create();
        CustomField::factory()->count(1)->testCheckbox()->create();
        CustomField::factory()->count(1)->testRadio()->create();


        DB::table('custom_field_custom_fieldset')->insert([
            [
                'custom_field_id' => '1',
                'custom_fieldset_id' => '1',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '2',
                'custom_fieldset_id' => '1',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '3',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '4',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '5',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],

            [
                'custom_field_id' => '6',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],

            [
                'custom_field_id' => '6',
                'custom_fieldset_id' => '1',
                'order' => 0,
                'required' => 0,
            ],

            [
                'custom_field_id' => '7',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '7',
                'custom_fieldset_id' => '1',
                'order' => 0,
                'required' => 0,
            ],

            [
                'custom_field_id' => '8',
                'custom_fieldset_id' => '2',
                'order' => 0,
                'required' => 0,
            ],
            [
                'custom_field_id' => '8',
                'custom_fieldset_id' => '1',
                'order' => 0,
                'required' => 0,
            ],

        ]);

        $assetDetailFields = [
            ['name' => 'CPU'],
            ['name' => 'RAM (GB)'],
            ['name' => 'HDD (GB)'],
            ['name' => 'System Type'],
            [
                'name' => 'Device Type',
                'element' => 'listbox',
                'field_values' => "Personal\r\nIT Devices",
                'show_in_listview' => true,
            ],
            [
                'name' => 'FA Asset',
                'element' => 'listbox',
                'field_values' => "YA\r\nTidak",
                'show_in_listview' => true,
            ],
            ['name' => 'On Behalf'],
            ['name' => 'Loc. Detail'],
            ['name' => 'Deployment Date', 'format' => 'DATE'],
            [
                'name' => 'Source',
                'element' => 'listbox',
                'field_values' => "New\r\nExisting",
            ],
        ];

        foreach ($assetDetailFields as $order => $attributes) {
            $field = CustomField::factory()->create(array_merge([
                'format' => 'ANY',
                'element' => 'text',
                'is_unique' => false,
                'field_encrypted' => false,
                'show_in_email' => false,
                'display_in_user_view' => false,
                'show_in_listview' => false,
                'show_in_requestable_list' => false,
            ], $attributes));

            $assetDetailFieldset->fields()->attach($field->id, [
                'order' => $order,
                'required' => false,
            ]);
        }
    }
}
