<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('feedimages', 'APIFeedImagesController@index')->name('feedimages.index');
    Route::post('feedimages/create', 'APIFeedImagesController@create')->name('feedimages.create');
    Route::post('feedimages/edit', 'APIFeedImagesController@edit')->name('feedimages.edit');
    Route::post('feedimages/show', 'APIFeedImagesController@show')->name('feedimages.show');
    Route::post('feedimages/delete', 'APIFeedImagesController@delete')->name('feedimages.delete');
});
?>