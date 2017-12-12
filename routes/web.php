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

Route::get('/', function () {
   // return view('index',['name' => 'xzz']);
    return redirect('home');
});
/*Route::get('/', function () {
    return redirect()->action('TestController@test'); // redirect的必需已经定义过路由
});*/
/*Route::get('/', function () {
    return redirect('test'); // redirect的必需已经定义过路由
});*/
Route::any('test','TestController@test', function () {
    return 'sdfdsfdsf';
});
Route::any('synclg','TestController@getLgPositions', function () {
    //
});
Route::any('w2t/{path}','TestController@wordToTxt', function () {
    //把word文档转成txt格式
});

Route::any('registerNamecheck', 'Auth\RegisterController@namecheck');
Route::any('getRegisterQuestions', 'Auth\ForgotPasswordController@getRegisterQuestions');
/*Route::namespace('Auth')->group(function () {
    // 在 "App\Http\Controllers\Admin" 命名空间下的控制器
    Route::post('register/namecheck', 'RegisterController@namecheck');
});*/



Auth::routes();//定义在/vendor/laravel/framework/src/Illuminate/Routing/Router.php中

Route::get('/home', 'HomeController@index')->name('home');
//发送短信接口
Route::post('phone/code', 'ApiController@sendVerifyCode');

//注册时名称唯一性验证
