<?php

use App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {

    Route::group(['middleware' => 'auth:api'], function () {

        /*
        * Companies API routes
        */
        Route::group(['prefix' => 'companies'], function () {
            Route::get('/', [Api\CompaniesController::class, 'index'])
                ->name('api.companies.index');
            Route::post('/', [Api\CompaniesController::class, 'store'])
                ->name('api.companies.store');
            Route::get('/{company}', [Api\CompaniesController::class, 'show'])
                ->name('api.companies.show');
            Route::patch('/{company}', [Api\CompaniesController::class, 'update'])
                ->name('api.companies.update');
            Route::delete('/{company}', [Api\CompaniesController::class, 'destroy'])
                ->name('api.companies.destroy');
            Route::post('/{company}/restore', [Api\CompaniesController::class, 'restore'])
                ->name('api.companies.restore');
        });

        /*
        * Categories API routes
        */
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', [Api\CategoriesController::class, 'index'])
                ->name('api.categories.index');
            Route::post('/', [Api\CategoriesController::class, 'store'])
                ->name('api.categories.store');
            Route::get('/{category}', [Api\CategoriesController::class, 'show'])
                ->name('api.categories.show');
            Route::patch('/{category}', [Api\CategoriesController::class, 'update'])
                ->name('api.categories.update');
            Route::delete('/{category}', [Api\CategoriesController::class, 'destroy'])
                ->name('api.categories.destroy');
            Route::post('/{category}/restore', [Api\CategoriesController::class, 'restore'])
                ->name('api.categories.restore');
        });

        /*
        * Locations API routes
        */
        Route::group(['prefix' => 'locations'], function () {
            Route::get('/', [Api\LocationsController::class, 'index'])
                ->name('api.locations.index');
            Route::post('/', [Api\LocationsController::class, 'store'])
                ->name('api.locations.store');
            Route::get('/{location}', [Api\LocationsController::class, 'show'])
                ->name('api.locations.show');
            Route::patch('/{location}', [Api\LocationsController::class, 'update'])
                ->name('api.locations.update');
            Route::delete('/{location}', [Api\LocationsController::class, 'destroy'])
                ->name('api.locations.destroy');
            Route::post('/{location}/restore', [Api\LocationsController::class, 'restore'])
                ->name('api.locations.restore');
        });

        /*
        * Accessories API routes
        */
        Route::group(['prefix' => 'accessories'], function () {
            Route::get('/', [Api\AccessoriesController::class, 'index'])
                ->name('api.accessories.index');
            Route::post('/', [Api\AccessoriesController::class, 'store'])
                ->name('api.accessories.store');
            Route::get('/{accessory}', [Api\AccessoriesController::class, 'show'])
                ->name('api.accessories.show');
            Route::patch('/{accessory}', [Api\AccessoriesController::class, 'update'])
                ->name('api.accessories.update');
            Route::delete('/{accessory}', [Api\AccessoriesController::class, 'destroy'])
                ->name('api.accessories.destroy');
            Route::post('/{accessory}/restore', [Api\AccessoriesController::class, 'restore'])
                ->name('api.accessories.restore');
            Route::post('/{accessory}/checkout', [Api\AccessoriesController::class, 'checkout'])
                ->name('api.accessories.checkout');
            Route::post('/{accessory}/checkin', [Api\AccessoriesController::class, 'checkin'])
                ->name('api.accessories.checkin');
        });

        /*
        * Consumables API routes
        */
        Route::group(['prefix' => 'consumables'], function () {
            Route::get('/', [Api\ConsumablesController::class, 'index'])
                ->name('api.consumables.index');
            Route::post('/', [Api\ConsumablesController::class, 'store'])
                ->name('api.consumables.store');
            Route::get('/{consumable}', [Api\ConsumablesController::class, 'show'])
                ->name('api.consumables.show');
            Route::patch('/{consumable}', [Api\ConsumablesController::class, 'update'])
                ->name('api.consumables.update');
            Route::delete('/{consumable}', [Api\ConsumablesController::class, 'destroy'])
                ->name('api.consumables.destroy');
            Route::post('/{consumable}/restore', [Api\ConsumablesController::class, 'restore'])
                ->name('api.consumables.restore');
            Route::post('/{consumable}/checkout', [Api\ConsumablesController::class, 'checkout'])
                ->name('api.consumables.checkout');
        });

        /*
        * Components API routes
        */
        Route::group(['prefix' => 'components'], function () {
            Route::get('/', [Api\ComponentsController::class, 'index'])
                ->name('api.components.index');
            Route::post('/', [Api\ComponentsController::class, 'store'])
                ->name('api.components.store');
            Route::get('/{component}', [Api\ComponentsController::class, 'show'])
                ->name('api.components.show');
            Route::patch('/{component}', [Api\ComponentsController::class, 'update'])
                ->name('api.components.update');
            Route::delete('/{component}', [Api\ComponentsController::class, 'destroy'])
                ->name('api.components.destroy');
            Route::post('/{component}/restore', [Api\ComponentsController::class, 'restore'])
                ->name('api.components.restore');
            Route::post('/{component}/checkout', [Api\ComponentsController::class, 'checkout'])
                ->name('api.components.checkout');
            Route::post('/{component}/checkin', [Api\ComponentsController::class, 'checkin'])
                ->name('api.components.checkin');
        });

        /*
        * Hardware/Assets API routes
        */
        Route::group(['prefix' => 'hardware'], function () {
            Route::get('/', [Api\AssetsController::class, 'index'])
                ->name('api.assets.index');
            Route::post('/', [Api\AssetsController::class, 'store'])
                ->name('api.assets.store');
            Route::get('/{asset}', [Api\AssetsController::class, 'show'])
                ->name('api.assets.show');
            Route::patch('/{asset}', [Api\AssetsController::class, 'update'])
                ->name('api.assets.update');
            Route::delete('/{asset}', [Api\AssetsController::class, 'destroy'])
                ->name('api.assets.destroy');
            Route::post('/{asset}/restore', [Api\AssetsController::class, 'restore'])
                ->name('api.assets.restore');
            Route::post('/{asset}/checkout', [Api\AssetsController::class, 'checkout'])
                ->name('api.assets.checkout');
            Route::post('/{asset}/checkin', [Api\AssetsController::class, 'checkin'])
                ->name('api.assets.checkin');
            Route::get('/{asset}/audit', [Api\AssetsController::class, 'audit'])
                ->name('api.assets.audit');
            Route::post('/{asset}/audit', [Api\AssetsController::class, 'auditStore'])
                ->name('api.assets.audit.store');
        });

        /*
        * Licenses API routes
        */
        Route::group(['prefix' => 'licenses'], function () {
            Route::get('/', [Api\LicensesController::class, 'index'])
                ->name('api.licenses.index');
            Route::post('/', [Api\LicensesController::class, 'store'])
                ->name('api.licenses.store');
            Route::get('/{license}', [Api\LicensesController::class, 'show'])
                ->name('api.licenses.show');
            Route::patch('/{license}', [Api\LicensesController::class, 'update'])
                ->name('api.licenses.update');
            Route::delete('/{license}', [Api\LicensesController::class, 'destroy'])
                ->name('api.licenses.destroy');
            Route::post('/{license}/restore', [Api\LicensesController::class, 'restore'])
                ->name('api.licenses.restore');
            Route::post('/{license}/checkout', [Api\LicensesController::class, 'checkout'])
                ->name('api.licenses.checkout');
            Route::post('/{license}/checkin', [Api\LicensesController::class, 'checkin'])
                ->name('api.licenses.checkin');
        });

        /*
        * License Seats API routes
        */
        Route::group(['prefix' => 'license-seats'], function () {
            Route::get('/', [Api\LicenseSeatsController::class, 'index'])
                ->name('api.license-seats.index');
            Route::get('/{licenseSeat}', [Api\LicenseSeatsController::class, 'show'])
                ->name('api.license-seats.show');
            Route::patch('/{licenseSeat}', [Api\LicenseSeatsController::class, 'update'])
                ->name('api.license-seats.update');
        });

        /*
        * Users API routes
        */
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [Api\UsersController::class, 'index'])
                ->name('api.users.index');
            Route::post('/', [Api\UsersController::class, 'store'])
                ->name('api.users.store');
            Route::get('/{user}', [Api\UsersController::class, 'show'])
                ->name('api.users.show');
            Route::patch('/{user}', [Api\UsersController::class, 'update'])
                ->name('api.users.update');
            Route::delete('/{user}', [Api\UsersController::class, 'destroy'])
                ->name('api.users.destroy');
            Route::post('/{user}/restore', [Api\UsersController::class, 'restore'])
                ->name('api.users.restore');
            Route::get('/{user}/assets', [Api\UsersController::class, 'assets'])
                ->name('api.users.assets');
            Route::get('/{user}/accessories', [Api\UsersController::class, 'accessories'])
                ->name('api.users.accessories');
            Route::get('/{user}/licenses', [Api\UsersController::class, 'licenses'])
                ->name('api.users.licenses');
        });

        /*
        * Groups API routes
        */
        Route::group(['prefix' => 'groups'], function () {
            Route::get('/', [Api\GroupsController::class, 'index'])
                ->name('api.groups.index');
            Route::post('/', [Api\GroupsController::class, 'store'])
                ->name('api.groups.store');
            Route::get('/{group}', [Api\GroupsController::class, 'show'])
                ->name('api.groups.show');
            Route::patch('/{group}', [Api\GroupsController::class, 'update'])
                ->name('api.groups.update');
            Route::delete('/{group}', [Api\GroupsController::class, 'destroy'])
                ->name('api.groups.destroy');
        });

        /*
        * Status Labels API routes
        */
        Route::group(['prefix' => 'statuslabels'], function () {
            Route::get('/', [Api\StatuslabelsController::class, 'index'])
                ->name('api.statuslabels.index');
            Route::post('/', [Api\StatuslabelsController::class, 'store'])
                ->name('api.statuslabels.store');
            Route::get('/{statuslabel}', [Api\StatuslabelsController::class, 'show'])
                ->name('api.statuslabels.show');
            Route::patch('/{statuslabel}', [Api\StatuslabelsController::class, 'update'])
                ->name('api.statuslabels.update');
            Route::delete('/{statuslabel}', [Api\StatuslabelsController::class, 'destroy'])
                ->name('api.statuslabels.destroy');
            
            // Additional statuslabels routes
            Route::get('/assets/bytype', [Api\StatuslabelsController::class, 'getAssetCountByStatusType'])
                ->name('api.statuslabels.assets.bytype');
            Route::get('/assets/byname', [Api\StatuslabelsController::class, 'getAssetCountByStatusName'])
                ->name('api.statuslabels.assets.byname');
                
            // NEW: Hybrid route for dashboard chart
            Route::get('/assets/hybrid', [Api\StatuslabelsController::class, 'getAssetCountByHybridStatus'])
                ->name('api.statuslabels.assets.byhybrid');
        });

        /*
        * Asset Models API routes
        */
        Route::group(['prefix' => 'models'], function () {
            Route::get('/', [Api\AssetModelsController::class, 'index'])
                ->name('api.models.index');
            Route::post('/', [Api\AssetModelsController::class, 'store'])
                ->name('api.models.store');
            Route::get('/{model}', [Api\AssetModelsController::class, 'show'])
                ->name('api.models.show');
            Route::patch('/{model}', [Api\AssetModelsController::class, 'update'])
                ->name('api.models.update');
            Route::delete('/{model}', [Api\AssetModelsController::class, 'destroy'])
                ->name('api.models.destroy');
            Route::post('/{model}/restore', [Api\AssetModelsController::class, 'restore'])
                ->name('api.models.restore');
            Route::get('/{model}/assets', [Api\AssetModelsController::class, 'assets'])
                ->name('api.models.assets');
        });

        /*
        * Asset Maintenances API routes
        */
        Route::group(['prefix' => 'maintenances'], function () {
            Route::get('/', [Api\AssetMaintenancesController::class, 'index'])
                ->name('api.maintenances.index');
            Route::post('/', [Api\AssetMaintenancesController::class, 'store'])
                ->name('api.maintenances.store');
            Route::get('/{maintenance}', [Api\AssetMaintenancesController::class, 'show'])
                ->name('api.maintenances.show');
            Route::patch('/{maintenance}', [Api\AssetMaintenancesController::class, 'update'])
                ->name('api.maintenances.update');
            Route::delete('/{maintenance}', [Api\AssetMaintenancesController::class, 'destroy'])
                ->name('api.maintenances.destroy');
        });

        /*
        * Manufacturers API routes
        */
        Route::group(['prefix' => 'manufacturers'], function () {
            Route::get('/', [Api\ManufacturersController::class, 'index'])
                ->name('api.manufacturers.index');
            Route::post('/', [Api\ManufacturersController::class, 'store'])
                ->name('api.manufacturers.store');
            Route::get('/{manufacturer}', [Api\ManufacturersController::class, 'show'])
                ->name('api.manufacturers.show');
            Route::patch('/{manufacturer}', [Api\ManufacturersController::class, 'update'])
                ->name('api.manufacturers.update');
            Route::delete('/{manufacturer}', [Api\ManufacturersController::class, 'destroy'])
                ->name('api.manufacturers.destroy');
            Route::post('/{manufacturer}/restore', [Api\ManufacturersController::class, 'restore'])
                ->name('api.manufacturers.restore');
        });

        /*
        * Suppliers API routes
        */
        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/', [Api\SuppliersController::class, 'index'])
                ->name('api.suppliers.index');
            Route::post('/', [Api\SuppliersController::class, 'store'])
                ->name('api.suppliers.store');
            Route::get('/{supplier}', [Api\SuppliersController::class, 'show'])
                ->name('api.suppliers.show');
            Route::patch('/{supplier}', [Api\SuppliersController::class, 'update'])
                ->name('api.suppliers.update');
            Route::delete('/{supplier}', [Api\SuppliersController::class, 'destroy'])
                ->name('api.suppliers.destroy');
            Route::post('/{supplier}/restore', [Api\SuppliersController::class, 'restore'])
                ->name('api.suppliers.restore');
        });

        /*
        * Departments API routes
        */
        Route::group(['prefix' => 'departments'], function () {
            Route::get('/', [Api\DepartmentsController::class, 'index'])
                ->name('api.departments.index');
            Route::post('/', [Api\DepartmentsController::class, 'store'])
                ->name('api.departments.store');
            Route::get('/{department}', [Api\DepartmentsController::class, 'show'])
                ->name('api.departments.show');
            Route::patch('/{department}', [Api\DepartmentsController::class, 'update'])
                ->name('api.departments.update');
            Route::delete('/{department}', [Api\DepartmentsController::class, 'destroy'])
                ->name('api.departments.destroy');
            Route::post('/{department}/restore', [Api\DepartmentsController::class, 'restore'])
                ->name('api.departments.restore');
        });

        /*
        * Custom Fields API routes
        */
        Route::group(['prefix' => 'fields'], function () {
            Route::get('/', [Api\CustomFieldsController::class, 'index'])
                ->name('api.customfields.index');
            Route::post('/', [Api\CustomFieldsController::class, 'store'])
                ->name('api.customfields.store');
            Route::get('/{field}', [Api\CustomFieldsController::class, 'show'])
                ->name('api.customfields.show');
            Route::patch('/{field}', [Api\CustomFieldsController::class, 'update'])
                ->name('api.customfields.update');
            Route::delete('/{field}', [Api\CustomFieldsController::class, 'destroy'])
                ->name('api.customfields.destroy');
            Route::post('/{field}/restore', [Api\CustomFieldsController::class, 'restore'])
                ->name('api.customfields.restore');
        });

        /*
        * Custom Fieldsets API routes
        */
        Route::group(['prefix' => 'fieldsets'], function () {
            Route::get('/', [Api\CustomFieldsetsController::class, 'index'])
                ->name('api.fieldsets.index');
            Route::post('/', [Api\CustomFieldsetsController::class, 'store'])
                ->name('api.fieldsets.store');
            Route::get('/{fieldset}', [Api\CustomFieldsetsController::class, 'show'])
                ->name('api.fieldsets.show');
            Route::patch('/{fieldset}', [Api\CustomFieldsetsController::class, 'update'])
                ->name('api.fieldsets.update');
            Route::delete('/{fieldset}', [Api\CustomFieldsetsController::class, 'destroy'])
                ->name('api.fieldsets.destroy');
            Route::post('/{fieldset}/restore', [Api\CustomFieldsetsController::class, 'restore'])
                ->name('api.fieldsets.restore');
        });

        /*
        * Activity/Reports API routes
        */
        Route::group(['prefix' => 'reports'], function () {
            Route::get('/activity', [Api\ReportsController::class, 'activity'])
                ->name('api.activity.index');
            Route::get('/depreciation', [Api\ReportsController::class, 'depreciation'])
                ->name('api.reports.depreciation');
            Route::get('/licenses', [Api\ReportsController::class, 'licenses'])
                ->name('api.reports.licenses');
            Route::get('/unaccepted_assets', [Api\ReportsController::class, 'unacceptedAssets'])
                ->name('api.reports.unaccepted_assets');
        });

        /*
        * Settings API routes
        */
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', [Api\SettingsController::class, 'index'])
                ->name('api.settings.index');
            Route::patch('/', [Api\SettingsController::class, 'update'])
                ->name('api.settings.update');
            Route::get('/ldaptest', [Api\SettingsController::class, 'ldapAdTest'])
                ->name('api.settings.ldaptest');
        });

        /*
        * SCIM API routes
        */
        Route::group(['prefix' => 'scim'], function () {
            Route::get('/users', [Api\ScimController::class, 'users'])
                ->name('api.scim.users');
            Route::post('/users', [Api\ScimController::class, 'createUser'])
                ->name('api.scim.users.create');
            Route::get('/users/{id}', [Api\ScimController::class, 'user'])
                ->name('api.scim.users.show');
            Route::put('/users/{id}', [Api\ScimController::class, 'updateUser'])
                ->name('api.scim.users.update');
            Route::patch('/users/{id}', [Api\ScimController::class, 'patchUser'])
                ->name('api.scim.users.patch');
            Route::delete('/users/{id}', [Api\ScimController::class, 'deleteUser'])
                ->name('api.scim.users.delete');
        });

        /*
        * Import API routes
        */
        Route::group(['prefix' => 'import'], function () {
            Route::post('/process', [Api\ImportController::class, 'process'])
                ->name('api.import.process');
            Route::get('/history', [Api\ImportController::class, 'history'])
                ->name('api.import.history');
        });

        /*
        * Asset Files/Upload API routes
        */
        Route::group(['prefix' => 'uploads'], function () {
            Route::post('/assets/{asset}', [Api\AssetFilesController::class, 'store'])
                ->name('api.assets.files.store');
            Route::get('/assets/{asset}', [Api\AssetFilesController::class, 'index'])
                ->name('api.assets.files.index');
            Route::delete('/assets/{asset}/{file}', [Api\AssetFilesController::class, 'destroy'])
                ->name('api.assets.files.destroy');
        });

        /*
        * Backup API routes
        */
        Route::group(['prefix' => 'backups'], function () {
            Route::get('/', [Api\BackupsController::class, 'index'])
                ->name('api.backups.index');
            Route::post('/', [Api\BackupsController::class, 'store'])
                ->name('api.backups.store');
            Route::post('/restore', [Api\BackupsController::class, 'restore'])
                ->name('api.backups.restore');
            Route::delete('/{backup}', [Api\BackupsController::class, 'destroy'])
                ->name('api.backups.destroy');
            Route::get('/download/{backup}', [Api\BackupsController::class, 'download'])
                ->name('api.backups.download');
        });

    });

    /*
    * Routes that don't require authentication
    */
    Route::post('/login', [Api\AuthController::class, 'login'])
        ->name('api.login');
    Route::post('/logout', [Api\AuthController::class, 'logout'])
        ->name('api.logout');
    Route::post('/refresh', [Api\AuthController::class, 'refresh'])
        ->name('api.refresh');
    Route::get('/user', [Api\AuthController::class, 'user'])
        ->name('api.user');

    /*
    * Health check route
    */
    Route::get('/health', function() {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => config('app.version', '1.0.0')
        ]);
    })->name('api.health');

});

/*
|--------------------------------------------------------------------------
| Fallback Routes
|--------------------------------------------------------------------------
*/

Route::fallback(function(){
    return response()->json([
        'error' => 'Not Found',
        'message' => 'The requested endpoint does not exist.'
    ], 404);
});