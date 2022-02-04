<?php

Route::group([
    'namespace' => 'Denngarr\Seat\Fitting\Http\Controllers',
    'middleware' => ['web', 'auth'],
    'prefix' => 'api/v2/fitting/web',
], function () {
    Route::get('/fitting/list', [
        'as' => 'fitting.api.web.fitting.list',
        'uses' => 'ApiFittingController@getFittingList',
    ]);
    Route::get('/fitting/get/{id}', [
        'as' => 'fitting.api.web.fitting.get',
        'uses' => 'ApiFittingController@getFittingById',
    ]);
    Route::get('/doctrine/list', [
        'as' => 'fitting.api.web.doctrine.list',
        'uses' => 'ApiFittingController@getDoctrineList',
    ]);
    Route::get('/doctrine/get/{id}', [
        'as' => 'fitting.api.web.doctrine.get',
        'uses' => 'ApiFittingController@getDoctrineById',
    ]);
});

Route::group([
    'namespace' => 'Denngarr\Seat\Fitting\Http\Controllers',
    'prefix' => 'fitting'
], function () {
    Route::group([
        'middleware' => ['web', 'auth'],
    ], function () {
        Route::get('/', [
            'as'   => 'fitting.view',
            'uses' => 'FittingController@getFittingView',
            'middleware' => 'can:fitting.view'
        ]);
        Route::get('/about', [
            'as'   => 'fitting.about',
            'uses' => 'FittingController@getAboutView',
            'middleware' => 'can:fitting.view'
        ]);
        Route::post('/postfitting', [
            'as'   => 'fitting.postFitting',
            'uses' => 'FittingController@postFitting',
            'middleware' => 'can:fitting.view'
        ]);
        Route::post('/postskills', [
            'as'   => 'fitting.postSkills',
            'uses' => 'FittingController@postSkills',
            'middleware' => 'can:fitting.view'
        ]);
        Route::post('/savefitting', [
            'as'   => 'fitting.saveFitting',
            'uses' => 'FittingController@saveFitting',
            'middleware' => 'can:fitting.create'
        ]);
        Route::get('/getfittingbyid/{id}', [
            'uses' => 'FittingController@getFittingById',
            'middleware' => 'can:fitting.doctrineview'
        ]);
        Route::get('/getfittingcostbyid/{id}', [
            'as' => 'fitting.appraiseFitting',
            'uses' => 'FittingController@getFittingCostById',
            'middleware' => 'can:fitting.doctrineview'
        ]);
        Route::get('/getdoctrinebyid/{id}', [
            'as'   => 'fitting.getDoctrineById',
            'uses' => 'FittingController@getDoctrineById',
            'middleware' => 'can:fitting.doctrineview'
        ]);
        Route::get('/geteftfittingbyid/{id}', [
            'uses' => 'FittingController@getEftFittingById',
            'middleware' => 'can:fitting.view'
        ]);
        Route::get('/getskillsbyfitid/{id}', [
            'uses' => 'FittingController@getSkillsByFitId',
            'middleware' => 'can:fitting.doctrineview'
        ]);
        Route::get('/delfittingbyid/{id}', [
            'uses' => 'FittingController@deleteFittingById',
            'middleware' => 'can:fitting.create'
        ]);
        Route::get('/doctrine', [
            'as'   => 'fitting.doctrineview',
            'uses' => 'FittingController@getDoctrineView',
            'middleware' => 'can:fitting.doctrineview'
        ]);
        Route::get('/fittinglist', [
            'as'   => 'fitting.fitlist',
            'uses' => 'FittingController@getFittingList',
            'middleware' => 'can:fitting.view'
        ]);
        Route::get('/rolelist', [
            'as'   => 'fitting.rolelist',
            'uses' => 'FittingController@getRoleList',
            'middleware' => 'can:fitting.view'
        ]);
        Route::post('/addDoctrine', [
            'as'   => 'fitting.addDoctrine',
            'uses' => 'FittingController@saveDoctrine',
            'middleware' => 'can:fitting.create'
        ]);
        Route::get('/getdoctrineedit/{id}', [
            'as'   => 'fitting.getDoctrineEdit',
            'uses' => 'FittingController@getDoctrineEdit',
            'middleware' => 'can:fitting.create'
        ]);
        Route::get('/deldoctrinebyid/{id}', [
            'as'   => 'fitting.delDoctrineById',
            'uses' => 'FittingController@delDoctrineById',
            'middleware' => 'can:fitting.create'
        ]);
        Route::get('/doctrineReport', [
            'as'   => 'fitting.doctrinereport',
            'uses' => 'FittingController@viewDoctrineReport',
            'middleware' => 'can:fitting.reportview'
        ]);
        Route::get('/runReport/{allianceid}/{corpid}/{doctrineid}', [
            'as'   => 'fitting.runreport',
            'uses' => 'FittingController@runReport',
            'middleware' => 'can:fitting.reportview'
        ]);
    });
});
