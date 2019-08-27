<?php

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
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::post('/postfitting', [
            'as'   => 'fitting.postFitting',
            'uses' => 'FittingController@postFitting',
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::post('/postskills', [
            'as'   => 'fitting.postSkills',
            'uses' => 'FittingController@postSkills',
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::post('/savefitting', [
            'as'   => 'fitting.saveFitting',
            'uses' => 'FittingController@saveFitting',
            'middleware' => 'bouncer:fitting.create'
        ]);
        Route::get('/getfittingbyid/{id}', [
            'uses' => 'FittingController@getFittingById',
            'middleware' => 'bouncer:fitting.doctrineview'
        ]);
        Route::get('/getdoctrinebyid/{id}', [
            'as'   => 'fitting.getDoctrineById',
            'uses' => 'FittingController@getDoctrineById',
            'middleware' => 'bouncer:fitting.doctrineview'
        ]);
        Route::get('/geteftfittingbyid/{id}', [
            'uses' => 'FittingController@getEftFittingById',
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::get('/getskillsbyfitid/{id}', [
            'uses' => 'FittingController@getSkillsByFitId',
            'middleware' => 'bouncer:fitting.doctrineview'
        ]);
        Route::get('/delfittingbyid/{id}', [
            'uses' => 'FittingController@deleteFittingById',
            'middleware' => 'bouncer:fitting.create'
        ]);
        Route::get('/doctrine', [
            'as'   => 'fitting.doctrineview',
            'uses' => 'FittingController@getDoctrineView',
            'middleware' => 'bouncer:fitting.doctrineview'
        ]);
        Route::get('/fittinglist', [
            'as'   => 'fitting.fitlist',
            'uses' => 'FittingController@getFittingList',
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::get('/rolelist', [
            'as'   => 'fitting.rolelist',
            'uses' => 'FittingController@getRoleList',
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::post('/addDoctrine', [
            'as'   => 'fitting.addDoctrine',
            'uses' => 'FittingController@saveDoctrine',
            'middleware' => 'bouncer:fitting.create'
        ]);
        Route::get('/getdoctrineedit/{id}', [
            'as'   => 'fitting.getDoctrineEdit',
            'uses' => 'FittingController@getDoctrineEdit',
            'middleware' => 'bouncer:fitting.create'
        ]);
        Route::get('/deldoctrinebyid/{id}', [
            'as'   => 'fitting.delDoctrineById',
            'uses' => 'FittingController@delDoctrineById',
            'middleware' => 'bouncer:fitting.create'
        ]);
        Route::get('/doctrineReport', [
            'as'   => 'fitting.doctrinereport',
            'uses' => 'FittingController@viewDoctrineReport',
            'middleware' => 'bouncer:fitting.reportview'
        ]);
        Route::get('/runReport/{allianceid}/{corpid}/{doctrineid}', [
            'as'   => 'fitting.runreport',
            'uses' => 'FittingController@runReport',
            'middleware' => 'bouncer:fitting.reportview'
        ]);
    });
});
