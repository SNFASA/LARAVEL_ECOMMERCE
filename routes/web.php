<?php

use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // Category routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/getSlug', [CategoryController::class, 'getSlug'])->name('getSlug');
        //edit Category
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}/edit', [CategoryController::class, 'update'])->name('categories.update');
        //delete Category
        Route::delete('/categories/{category}/edit', [CategoryController::class, 'destroy'])->name('categories.delete');

        // Sub-category routes
        Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        // Create
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        //edit sub Category
        Route::get('/sub-categories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subcategory}/edit', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        //delete sub category 
        Route::delete('/sub-categories/{subcategory}/edit', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');

        //temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
        //brands routes 
        Route::get('/brand', [BrandController::class, 'index'])->name('brands.index');

        //create 
        Route::get('/brand/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brand', [BrandController::class, 'store'])->name('brands.store');

        //edit brand
        Route::get('/brand/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brand/{brand}', [BrandController::class, 'update'])->name('brands.update');

        //delete brand
        Route::delete('/brand/{brand}', [BrandController::class, 'destroy'])->name('brands.delete');

    });
});
