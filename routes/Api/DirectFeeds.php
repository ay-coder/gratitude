<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('directfeeds', 'APIDirectFeedsController@index')->name('directfeeds.index');

    Route::post('direct-feed/create', 'APIDirectFeedsController@create')->name('directfeeds.create');

    Route::post('directfeeds/create', 'APIDirectFeedsController@create')->name('directfeeds.create');


    Route::post('directfeeds/edit', 'APIDirectFeedsController@edit')->name('directfeeds.edit');
    Route::post('directfeeds/show', 'APIDirectFeedsController@show')->name('directfeeds.show');
    Route::post('directfeeds/delete', 'APIDirectFeedsController@delete')->name('directfeeds.delete');
});
?>