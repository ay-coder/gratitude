<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('block-users', 'APIBlockUsersController@index')->name('blockusers.index');

    Route::post('block-user/create', 'APIBlockUsersController@create')->name('blockusers.create');


    Route::post('blockusers/edit', 'APIBlockUsersController@edit')->name('blockusers.edit');
    Route::post('blockusers/show', 'APIBlockUsersController@show')->name('blockusers.show');
    Route::post('block-user/delete', 'APIBlockUsersController@delete')->name('blockusers.delete');
});
?>