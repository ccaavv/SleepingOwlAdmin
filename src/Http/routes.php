<?php

$router->group(['as' => 'admin.', 'namespace' => 'SleepingOwl\Admin\Http\Controllers'], function ($router) {
    if (! $router->has('admin.dashboard')) {
        $router->get('', ['as' => 'dashboard', 'uses' => 'AdminController@getDashboard']);
    }

    $router->get('{adminModel}', [
        'as'   => 'model',
        'uses' => 'AdminController@getDisplay',
    ]);

    $router->post('{adminModel}', [
        'as'   => 'model',
        'uses' => 'AdminController@inlineEdit',
    ]);

	$router->post('{adminModel}/custom', [
		'as'   => 'model.custom_display',
		'uses' => 'AdminController@displayUpdate',
	]);

    $router->get('{adminModel}/create', [
        'as'   => 'model.create',
        'uses' => 'AdminController@getCreate',
    ]);

    $router->post('{adminModel}/create', [
        'as'   => 'model.store',
        'uses' => 'AdminController@postStore',
    ]);

    $router->get('{adminModel}/{adminModelId}/edit', [
        'as'   => 'model.edit',
        'uses' => 'AdminController@getEdit',
    ]);

	$router->get('{adminModel}/{adminModelId}/duplicate', [
		'as'   => 'model.duplicate',
		'uses' => 'AdminController@getDuplicate',
	]);

	$router->get('{adminModel}/{adminModelId}/view', [
		'as'   => 'model.view',
		'uses' => 'AdminController@getView',
	]);

    $router->post('{adminModel}/{adminModelId}/edit', [
        'as'   => 'model.update',
        'uses' => 'AdminController@postUpdate',
    ]);

    $router->delete('{adminModel}/{adminModelId}/delete', [
        'as'   => 'model.delete',
        'uses' => 'AdminController@deleteDelete',
    ]);

    $router->delete('{adminModel}/{adminModelId}/destroy', [
        'as'   => 'model.destroy',
        'uses' => 'AdminController@deleteDestroy',
    ]);

    $router->post('{adminModel}/{adminModelId}/restore', [
        'as'   => 'model.restore',
        'uses' => 'AdminController@postRestore',
    ]);

    $router->get('{adminWildcard}', [
        'as'   => 'wildcard',
        'uses' => 'AdminController@getWildcard',
    ]);

	$router->post('/renew-image', [
		'as'   => 'renew-image',
		'uses' => 'UploadController@renewImage',
	]);
});
