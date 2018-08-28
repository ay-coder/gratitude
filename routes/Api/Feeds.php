<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('feeds', 'APIFeedsController@index')->name('feeds.index');
    Route::post('feeds/create', 'APIFeedsController@create')->name('feeds.create');
    Route::post('feeds/edit', 'APIFeedsController@edit')->name('feeds.edit');
    Route::post('feeds/show', 'APIFeedsController@show')->name('feeds.show');
    Route::post('feeds/delete', 'APIFeedsController@delete')->name('feeds.delete');
});
?>