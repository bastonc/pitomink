<?php

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

/**Route::get('/', function () {
    return view('index');
});**/

/*****Роуты к питомнику*****/
Route::get('/', function (){ return view('index');});
Route::post('addanimal', 'pitomnikController@addAnimal');
//Route::get('addanimal', 'pitomnikController@addAnimal');
Route::post('getall','pitomnikController@getAll');
//Route::get('getall','pitomnikController@getAll');
Route::post('animalincell','pitomnikController@animalInCell');
Route::post('givemoney','pitomnikController@giveMoney');
//Route::get('givemoney','pitomnikController@giveMoney');
Route::get('delanimal','pitomnikController@delAnimal');
Route::post('delanimal','pitomnikController@delAnimal');
Route::post('destroyer','pitomnikController@destroyer');
//Route::get('destroyer','pitomnikController@destroyer');





/*****Роуты к питомнику конец*****/




