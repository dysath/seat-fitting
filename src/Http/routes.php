<?PHP

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

    });
});
