<?php

use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\admin\MenuGroupController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\RoleController;
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



    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('test', function () {
        return 'Test Successful';
    })->name('test');
});

