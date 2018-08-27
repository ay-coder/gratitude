<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('blocked', 'APIBlockedController@index')->name('blocked.index');
    Route::post('blocked/create', 'APIBlockedController@create')->name('blocked.create');
    Route::post('blocked/edit', 'APIBlockedController@edit')->name('blocked.edit');
    Route::post('blocked/show', 'APIBlockedController@show')->name('blocked.show');
    Route::post('blocked/delete', 'APIBlockedController@delete')->name('blocked.delete');
});
?>