<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\DataCollector\RouterDataCollector;

/*p
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


Route::group([ 'middleware' => 'auth:api'],function(){
   // Make Route Post
   Route::post('post',[PostController::class,'store']);
   Route::get('posts',[PostController::class,'index']);
   Route::get('post/{id}',[PostController::class,'show']); // show
   Route::post('post/{id}',[PostController::class,'update']);  // if have file / photo use method post
   Route::delete('post/{id}',[PostController::class,'destory']);

   // Make Route Like
   Route::get('like/{id}', [LikeController::class,'getLikes']);
   Route::post('toggleLike/{id}', [LikeController::class,'toggleLike']);

   // Make Route Comment
   Route::get('comments/{id}', [CommentController::class,'show']);
   Route::post('comment/{id}', [CommentController::class,'store']);
   Route::put('comment/{id}', [CommentController::class,'update']);
   Route::delete('comment/{id}', [CommentController::class,'destory']);

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::get('/profile',function(){
        return response()->json([
            'message' => "He is handsome"
        ]);
    });
    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::post('refresh',[AuthController::class,'refresh']);
    Route::get('me',[AuthController::class,'me']);  //get current user logged in
});
