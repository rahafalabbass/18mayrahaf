<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\products\ProductController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\users\UserController;
use App\Http\Controllers\reviews\ReviewController;
use App\Http\Controllers\orders\OrderController;
use App\Http\Controllers\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout']);;
Route::post('/login',[AuthController::class,'login'])->name('login');


Route::group([

  //'prefix' => 'products',
  //  'middleware' => ['auth:sanctum','throttle:60,1']
    'middleware' => ['auth:sanctum']
],function (){
    Route::match(['put', 'patch'], '/update-user/{id}',[UserController::class,'updateRoles']);
    Route::group([
        'middleware' => 'isadmin'
    ],function(){
        Route::post('/add-product',[ProductController::class,'store']);
        Route::match(['put', 'patch'], '/update-product/{id}',[ProductController::class,'update']);
        Route::delete( '/delete-product/{id}',[ProductController::class,'destroy']);
        Route::get('/u',[UserController::class,'getUsersByRole']);
    });

    Route::get('/all-products',[ProductController::class,'index']);
  //There is something wrong right here....i wish you discover it
    Route::get('/product/{letter}',[ProductController::class,'filterProductsByCategory']);
    Route::get('/product/{id}',[ProductController::class,'show']);
    Route::get('/all-users',[UserController::class,'index']);

    Route::get('/reviews',[ReviewController::class,'index']);
    Route::post('/add_review/{product}',[ReviewController::class, 'store']);
    Route::get('/show_review/{id}',[ReviewController::class,'show']);
    Route::match(['patch','put'],'review/{id}',[ReviewController::class,'update']);
    Route::delete('del_review/{id}',[ReviewController::class,'destroy']);
    Route::get('/user-reviews/{user}',[ReviewController::class,'reviewsOfUser'])->middleware('isadmin');
    Route::get('/product_reviews/{id}',[ReviewController::class,'productReviews']);

    Route::get('/orders',[OrderController::class,'index'])->middleware('isadmin');
    Route::post('/add_order',[OrderController::class,'store']);
    Route::get('/show_order/{id}',[OrderController::class,'show']);
    Route::delete('del_order/{id}',[OrderController::class,'destroy'])->middleware('isadmin');
    Route::get('/get_order/{id}',[OrderController::class,'ordersOfUser']);
    Route::get('/user_order/{user}',[OrderController::class,'ordersOfUser'])->middleware('isadmin');

    Route::get('/vendors',[VendorController::class,'index']);
    Route::post('/add_vendor',[VendorController::class,'store'])->middleware('isadmin');
    Route::get('/products_vendor/{vendor}',[VendorController::class,'allProducts']);
    Route::get('/all_vendors/{product}',[VendorController::class,'allVendors']);


});


