<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('feedlike', 'APIFeedLikeController@index')->name('feedlike.index');

    Route::post('likepost', 'APIFeedLikeController@likePost')->name('feedlike.likepost');


    Route::post('feedlike/create', 'APIFeedLikeController@create')->name('feedlike.create');
    Route::post('feedlike/edit', 'APIFeedLikeController@edit')->name('feedlike.edit');
    Route::post('feedlike/show', 'APIFeedLikeController@show')->name('feedlike.show');
    Route::post('feedlike/delete', 'APIFeedLikeController@delete')->name('feedlike.delete');
});
?>