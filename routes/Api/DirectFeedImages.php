<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('directfeedimages', 'APIDirectFeedImagesController@index')->name('directfeedimages.index');
    Route::post('directfeedimages/create', 'APIDirectFeedImagesController@create')->name('directfeedimages.create');
    Route::post('directfeedimages/edit', 'APIDirectFeedImagesController@edit')->name('directfeedimages.edit');
    Route::post('directfeedimages/show', 'APIDirectFeedImagesController@show')->name('directfeedimages.show');
    Route::post('directfeedimages/delete', 'APIDirectFeedImagesController@delete')->name('directfeedimages.delete');
});
?>