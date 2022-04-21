<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\FraudController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::group(['middleware' => ['cors', 'json.response','auth:sanctum']], function () {
Route::group(['middleware' => ['cors', 'json.response','auth:sanctum']], function () {


});
Route::post('registration/user',[RegisterController::class,'register']);
Route::post('login/user',[RegisterController::class,'login']);
Route::post('fraud',[FraudController::class, 'FraudRegistration']);
Route::get('fraud/view/{id}',[FraudController::class, 'ViewFraudRequest']);
Route::post('fraud/update/{id}',[FraudController::class, 'UpdateFraudRequest']);
Route::post('fraud/delete/{id}', [FraudController::class, 'DeleteFraudRequest']);
