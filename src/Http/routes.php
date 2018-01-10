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
            'middleware' => 'bouncer:fitting.view'
        ]);
        Route::get('/getskillsbyfitid/{id}', [
            'uses' => 'FittingController@getSkillsByFitId',
            'middleware' => 'bouncer:fitting.view'
        ]);
    });
});
