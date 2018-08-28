<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('feedtagusers', 'APIFeedTagUsersController@index')->name('feedtagusers.index');
    Route::post('feedtagusers/create', 'APIFeedTagUsersController@create')->name('feedtagusers.create');
    Route::post('feedtagusers/edit', 'APIFeedTagUsersController@edit')->name('feedtagusers.edit');
    Route::post('feedtagusers/show', 'APIFeedTagUsersController@show')->name('feedtagusers.show');
    Route::post('feedtagusers/delete', 'APIFeedTagUsersController@delete')->name('feedtagusers.delete');
});
?>