<?php

Route::group(['namespace'=>'Api'],function (){
    Route::any('signup','Account\AccountController@register')->name('account.signup');//注册
    Route::any('login','Account\AccountController@login')->name('account.login');//登陆

    Route::group(['middleware' => 'jwt.auth'],function (){
        Route::any('authtoken','Account\AccountController@getAuthenticatedUser')->name('auth.token');//获取token用户信息
        Route::any('refreshToken','Account\AccountController@refreshToken')->name('refresh.token');//刷新token用户信息
        Route::any('bind/email','Bind\EmailController@bindEmail')->name('bind.email');//绑定邮箱
        Route::any('auth/email','Bind\EmailController@authEmail')->name('auth.email');//验证邮箱

    });



});
