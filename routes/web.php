<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AuthController;
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
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\shop\ShopOrderController;
use Illuminate\Support\Facades\Route;

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

Route::get('/test', function(){
    return 'test';
})->name('test.index');
Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user?->hasRole('shop-manager')) {
        return redirect()->route('shop.orders.index');
    }

    if ($user?->hasRole('super-admin')) {
        return redirect()->route('admin.dashboard');
    }

    foreach ([
        'inventory.view' => 'admin.warehouse.dashboard',
        'order.prepare' => 'admin.orders.warehouse-sheet',
        'order.view.all' => 'admin.orders.index',
        'shop.view' => 'admin.shops.index',
        'fruit.view' => 'admin.fruits.index',
        'category.view' => 'admin.categories.index',
        'box.view' => 'admin.boxes.index',
        'price.view' => 'admin.fruit-prices.index',
        'report.view' => 'admin.reports.daily',
        'role.view' => 'admin.roles.index',
        'user.view' => 'admin.users.index',
        'menu.view' => 'admin.menus.index',
        'permission.view' => 'admin.permissions.index',
    ] as $permission => $route) {
        if ($user?->can($permission)) {
            return redirect()->route($route);
        }
    }

    abort(403, 'No admin page permissions have been assigned to this account.');
})->name('home');
// Login and Authentication Routes
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginAll'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'update'])->name('password.change.update');
});

Route::middleware(['auth', 'password.change.required', 'role:shop-manager'])->prefix('shop')->name('shop.')->group(function () {
    Route::get('/orders', [ShopOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/history/data', [ShopOrderController::class, 'historyData'])->name('orders.history.data');
    Route::get('/orders/history/download', [ShopOrderController::class, 'downloadHistoryPdf'])->name('orders.history.pdf');
    Route::post('/orders/draft', [ShopOrderController::class, 'saveDraft'])->name('orders.draft');
    Route::post('/orders/submit', [ShopOrderController::class, 'submit'])->name('orders.submit');
    Route::get('/orders/{order}/pdf', [ShopOrderController::class, 'downloadPdf'])->name('orders.pdf');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'password.change.required', 'role:super-admin|warehouse-manager'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->middleware('role:super-admin')
        ->name('dashboard');
    Route::get('/warehouse', [AdminController::class, 'index'])
        ->middleware('permission:inventory.view')
        ->name('warehouse.dashboard');
    // Menu Management Routes

    Route::get('/menus', [MenuController::class, 'index'])
        ->middleware('permission:menu.view')
        ->name('menus.index');

    Route::post('/menus/store', [MenuController::class, 'storeAjax'])->middleware('permission:menu.create')->name('menus.store.ajax');
    Route::post('/menus/update/{id}', [MenuController::class, 'updateAjax'])->middleware('permission:menu.edit')->name('menus.update.ajax');
    Route::delete('/menus/delete/{id}', [MenuController::class, 'deleteAjax'])->middleware('permission:menu.delete')->name('menus.delete.ajax');

    // Menu Group Management Routes
    Route::get('/menu-groups', [MenuGroupController::class, 'index'])->middleware('permission:menu.view')->name('menu-groups.index');

    Route::post('/menu-groups/store', [MenuGroupController::class, 'storeAjax'])->middleware('permission:menu.create')->name('menu-groups.store.ajax');

    Route::post('/menu-groups/update/{id}', [MenuGroupController::class, 'updateAjax'])->middleware('permission:menu.edit')->name('menu-groups.update.ajax');

    Route::delete('/menu-groups/delete/{id}', [MenuGroupController::class, 'deleteAjax'])->middleware('permission:menu.delete')->name('menu-groups.delete.ajax');

    // Role Management Routes
    // Role Management Routes
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:role.view')->name('roles.index');

    Route::post('/roles/store', [RoleController::class, 'storeAjax'])->middleware('permission:role.create')->name('roles.store.ajax');

    Route::post('/roles/update/{id}', [RoleController::class, 'updateAjax'])->middleware('permission:role.edit')->name('roles.update.ajax');

    Route::delete('/roles/delete/{id}', [RoleController::class, 'deleteAjax'])->middleware('permission:role.delete')->name('roles.delete.ajax');

    // FIXED: Correct method name
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getRole'])->middleware('permission:role.view');

    // FIXED: Add missing method
    Route::post('/roles/{id}/permissions/update', [RoleController::class, 'updatePermissions'])->middleware('permission:role.edit');

    // Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:permission.view')->name('permissions.index');

    Route::post('/permissions/store', [PermissionController::class, 'storeAjax'])->middleware('permission:permission.create')->name('permissions.store.ajax');

    Route::post('/permissions/update/{id}', [PermissionController::class, 'updateAjax'])->middleware('permission:permission.edit')->name('permissions.update.ajax');

    Route::delete('/permissions/delete/{id}', [PermissionController::class, 'deleteAjax'])->middleware('permission:permission.delete')->name('permissions.delete.ajax');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:user.view')
        ->name('users.index');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('users.store');
    Route::post('/users/update/{id}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('users.update.ajax');
    Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('users.destroy.ajax');

    Route::prefix('shops')
        ->name('shops.')
        ->group(function () {
            Route::get('/', [ShopController::class, 'index'])->middleware('permission:shop.view')->name('index');

            Route::post('/store', [ShopController::class, 'store'])->middleware('permission:shop.create')->name('store');

            Route::get('/edit/{id}', [ShopController::class, 'edit'])->middleware('permission:shop.edit')->name('edit');

            Route::post('/update/{id}', [ShopController::class, 'update'])->middleware('permission:shop.edit')->name('update');

            Route::post('/{id}/reset-manager-password', [ShopController::class, 'resetManagerPassword'])->middleware('permission:shop.edit')->name('reset-manager-password');

            Route::delete('/delete/{id}', [ShopController::class, 'destroy'])->middleware('permission:shop.delete')->name('destroy');
        });
    Route::prefix('categories')->name('categories.')->middleware(['auth'])->group(function () {

        Route::get('/', [CategoryController::class, 'index'])->middleware('permission:category.view')->name('index');

        Route::post('/store', [CategoryController::class, 'store'])->middleware('permission:category.create')->name('store');

        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->middleware('permission:category.edit')->name('edit');

        Route::post('/update/{id}', [CategoryController::class, 'update'])->middleware('permission:category.edit')->name('update');

        Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->middleware('permission:category.delete')->name('destroy');

    });

    Route::prefix('fruits')->name('fruits.')->group(function () {
        Route::get('/', [FruitController::class, 'index'])->middleware('permission:fruit.view')->name('index');
        Route::post('/store', [FruitController::class, 'store'])->middleware('permission:fruit.create')->name('store');
        Route::get('/edit/{id}', [FruitController::class, 'edit'])->middleware('permission:fruit.edit')->name('edit');
        Route::post('/update/{id}', [FruitController::class, 'update'])->middleware('permission:fruit.edit')->name('update');
        Route::delete('/delete/{id}', [FruitController::class, 'destroy'])->middleware('permission:fruit.delete')->name('destroy');
    });

    Route::prefix('boxes')->name('boxes.')->group(function () {
        Route::get('/', [FruitBoxConfigurationController::class, 'index'])->middleware('permission:box.view')->name('index');
        Route::post('/store', [FruitBoxConfigurationController::class, 'store'])->middleware('permission:box.create')->name('store');
        Route::get('/edit/{id}', [FruitBoxConfigurationController::class, 'edit'])->middleware('permission:box.edit')->name('edit');
        Route::post('/update/{id}', [FruitBoxConfigurationController::class, 'update'])->middleware('permission:box.edit')->name('update');
        Route::delete('/delete/{id}', [FruitBoxConfigurationController::class, 'destroy'])->middleware('permission:box.delete')->name('destroy');
    });

    Route::prefix('fruit-prices')->name('fruit-prices.')->group(function () {
        Route::get('/', [FruitPriceController::class, 'index'])->middleware('permission:price.view')->name('index');
        Route::post('/store', [FruitPriceController::class, 'store'])->middleware('permission:price.create')->name('store');
        Route::get('/edit/{id}', [FruitPriceController::class, 'edit'])->middleware('permission:price.edit')->name('edit');
        Route::post('/update/{id}', [FruitPriceController::class, 'update'])->middleware('permission:price.edit')->name('update');
        Route::delete('/delete/{id}', [FruitPriceController::class, 'destroy'])->middleware('permission:price.delete')->name('destroy');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('index');
        Route::post('/movements', [InventoryController::class, 'storeMovement'])
            ->middleware('permission:inventory.create|inventory.edit|inventory.adjust')
            ->name('movements.store');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:order.view.all')->name('index');
        Route::get('/warehouse-sheet', [OrderController::class, 'warehouseSheet'])->middleware('permission:order.prepare')->name('warehouse-sheet');
        Route::post('/warehouse-weights', [OrderController::class, 'saveWarehouseWeights'])->middleware('permission:order.prepare')->name('warehouse-weights.save');
        Route::get('/warehouse-pdf', [OrderController::class, 'warehousePdf'])->middleware('permission:order.prepare')->name('warehouse-pdf');
        Route::get('/fruit-cajas-pdf', [OrderController::class, 'fruitCajasPdf'])->middleware('permission:order.prepare')->name('fruit-cajas-pdf');
        Route::get('/fruit-cajas-excel', [OrderController::class, 'fruitCajasExcel'])->middleware('permission:order.prepare')->name('fruit-cajas-excel');
        Route::get('/{id}', [OrderController::class, 'show'])->middleware('permission:order.view.all')->name('show');
        Route::post('/{id}/weights', [OrderController::class, 'updateWeights'])->middleware('permission:order.prepare')->name('weights');
        Route::get('/{id}/final-pdf', [OrderController::class, 'finalPdf'])->middleware('permission:order.view.all')->name('final-pdf');
        Route::post('/{id}/status', [OrderController::class, 'updateStatus'])
            ->middleware('permission:order.prepare|order.ready|order.send')
            ->name('status');
    });

    Route::get('/shortage', [ShortageController::class, 'index'])->middleware('permission:inventory.view')->name('shortage.index');

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
    })->middleware('role:super-admin')->name('test');
});


// use Illuminate\Support\Facades\Artisan;


// Route::get('refresh', function () {

//     Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
//     Artisan::call('optimize:clear');

//    return 'hi';
// });