<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\FruitBoxConfigurationController;
use App\Http\Controllers\admin\FruitController;
use App\Http\Controllers\admin\FruitPriceController;
use App\Http\Controllers\admin\InventoryController;
use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\admin\MenuGroupController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\ShopController;
use App\Http\Controllers\admin\ShortageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
// Login and Authentication Routes
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginAll'])->name('login.post');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/warehouse', [AdminController::class, 'index'])->name('warehouse.dashboard');
    // Menu Management Routes

    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');

    Route::post('/menus/store', [MenuController::class, 'storeAjax'])->name('menus.store.ajax');
    Route::post('/menus/update/{id}', [MenuController::class, 'updateAjax'])->name('menus.update.ajax');
    Route::delete('/menus/delete/{id}', [MenuController::class, 'deleteAjax'])->name('menus.delete.ajax');

    // Menu Group Management Routes
    Route::get('/menu-groups', [MenuGroupController::class, 'index'])->name('menu-groups.index');

    Route::post('/menu-groups/store', [MenuGroupController::class, 'storeAjax'])->name('menu-groups.store.ajax');

    Route::post('/menu-groups/update/{id}', [MenuGroupController::class, 'updateAjax'])->name('menu-groups.update.ajax');

    Route::delete('/menu-groups/delete/{id}', [MenuGroupController::class, 'deleteAjax'])->name('menu-groups.delete.ajax');

    // Role Management Routes
    // Role Management Routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

    Route::post('/roles/store', [RoleController::class, 'storeAjax'])->name('roles.store.ajax');

    Route::post('/roles/update/{id}', [RoleController::class, 'updateAjax'])->name('roles.update.ajax');

    Route::delete('/roles/delete/{id}', [RoleController::class, 'deleteAjax'])->name('roles.delete.ajax');

    // FIXED: Correct method name
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getRole']);

    // FIXED: Add missing method
    Route::post('/roles/{id}/permissions/update', [RoleController::class, 'updatePermissions']);

    // Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

    Route::post('/permissions/store', [PermissionController::class, 'storeAjax'])->name('permissions.store.ajax');

    Route::post('/permissions/update/{id}', [PermissionController::class, 'updateAjax'])->name('permissions.update.ajax');

    Route::delete('/permissions/delete/{id}', [PermissionController::class, 'deleteAjax'])->name('permissions.delete.ajax');



    Route::prefix('shops')
        ->name('shops.')
        ->group(function () {
            Route::get('/', [ShopController::class, 'index'])->name('index');

            Route::post('/store', [ShopController::class, 'store'])->name('store');

            Route::get('/edit/{id}', [ShopController::class, 'edit'])->name('edit');

            Route::post('/update/{id}', [ShopController::class, 'update'])->name('update');

            Route::delete('/delete/{id}', [ShopController::class, 'destroy'])->name('destroy');
        });
    Route::prefix('categories')->name('categories.')->middleware(['auth'])->group(function () {

        Route::get('/', [CategoryController::class, 'index'])->name('index');

        Route::post('/store', [CategoryController::class, 'store'])->name('store');

        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');

        Route::post('/update/{id}', [CategoryController::class, 'update'])->name('update');

        Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');

    });

    Route::prefix('fruits')->name('fruits.')->group(function () {
        Route::get('/', [FruitController::class, 'index'])->name('index');
        Route::post('/store', [FruitController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [FruitController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [FruitController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [FruitController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('boxes')->name('boxes.')->group(function () {
        Route::get('/', [FruitBoxConfigurationController::class, 'index'])->name('index');
        Route::post('/store', [FruitBoxConfigurationController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [FruitBoxConfigurationController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [FruitBoxConfigurationController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [FruitBoxConfigurationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('fruit-prices')->name('fruit-prices.')->group(function () {
        Route::get('/', [FruitPriceController::class, 'index'])->name('index');
        Route::post('/store', [FruitPriceController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [FruitPriceController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [FruitPriceController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [FruitPriceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/movements', [InventoryController::class, 'storeMovement'])->name('movements.store');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/status', [OrderController::class, 'updateStatus'])->name('status');
    });

    Route::get('/shortage', [ShortageController::class, 'index'])->name('shortage.index');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])
            ->middleware('permission:report.view')
            ->name('daily');
        Route::get('/pdf', [ReportController::class, 'pdf'])
            ->middleware('permission:report.pdf')
            ->name('pdf');
        Route::get('/excel', [ReportController::class, 'excel'])
            ->middleware('permission:report.excel')
            ->name('excel');
    });
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('test', function () {
        return 'Test Successful';
    })->name('test');
});
