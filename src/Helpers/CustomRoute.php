<?php
namespace Piclou\Piclommerce\Helpers;
use Illuminate\Support\Facades\Route;

class CustomRoute
{

    public static function auth()
    {
        /* Auth */
        Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('login', 'Auth\LoginController@login');
        Route::post('logout', 'Auth\LoginController@logout')->name('logout');
        Route::get('logout', 'Auth\LoginController@logout')->name('logout');
        Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'Auth\RegisterController@register');
        // Password Reset Routes...
        Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    }

    /**
     * Permet de générer le CRUD propre au système
     * @param string $uri
     * @param string $controller
     * @param string $name
     */
    public static function crud(string $uri, string $controller, string $name, $moduleName = null)
    {
        /* Liste */
        Route::get("/{$uri}", "{$controller}@index")
            ->name($name . ".index");
        /* Création */
        Route::get("/{$uri}/create","{$controller}@create")
            ->name($name . ".create");
        Route::post("/{$uri}/create","{$controller}@store")
            ->name($name . ".store");
        /* Edition */
        Route::get("/{$uri}/edit/{uuid}","{$controller}@edit")
            ->name($name . ".edit")
            ->where(['uuid' => '[a-z-0-9\-]+']);
        /* Edition - POST */
        Route::post("/{$uri}/update/{uuid}","{$controller}@update")
            ->name($name . ".update")
            ->where(['uuid' => '[a-z-0-9\-]+']);
        /* Suppression */
        Route::get("/{$uri}/delete/{uuid}","{$controller}@destroy")
            ->name($name . ".delete")
            ->where(['uuid' => '[a-z-0-9\-]+']);

    }
}
