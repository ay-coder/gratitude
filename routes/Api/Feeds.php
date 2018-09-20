<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('feeds', 'APIFeedsController@index')->name('feeds.index');

	Route::post('my-text-feeds', 'APIFeedsController@myTextFeeds')->name('feeds.my-text-feeds');

	Route::post('my-image-feeds', 'APIFeedsController@myImageFeeds')->name('feeds.my-image-feeds');

	Route::post('feeds/get-love-like', 'APIFeedsController@getLoveLike')->name('feeds.get-love-like');

    Route::post('feeds/create', 'APIFeedsController@create')->name('feeds.create');
    Route::post('feeds/edit', 'APIFeedsController@edit')->name('feeds.edit');
    Route::post('feeds/show', 'APIFeedsController@show')->name('feeds.show');
    Route::post('feeds/delete', 'APIFeedsController@delete')->name('feeds.delete');
});
?>