<?php
Route::group(['namespace' => 'Api'], function()
{
	Route::post('lovepost', 'APIFeedLoveController@lovePost')->name('feedlike.lovepost');

    Route::get('feedlove', 'APIFeedLoveController@index')->name('feedlove.index');
    Route::post('feedlove/create', 'APIFeedLoveController@create')->name('feedlove.create');
    Route::post('feedlove/edit', 'APIFeedLoveController@edit')->name('feedlove.edit');
    Route::post('feedlove/show', 'APIFeedLoveController@show')->name('feedlove.show');
    Route::post('feedlove/delete', 'APIFeedLoveController@delete')->name('feedlove.delete');
});
?>