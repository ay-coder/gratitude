<?php
Route::group(['namespace' => 'Api'], function()
{
    

    Route::post('categories/create', 'APICategoriesController@create')->name('categories.create');
    Route::post('categories/edit', 'APICategoriesController@edit')->name('categories.edit');
    Route::post('categories/show', 'APICategoriesController@show')->name('categories.show');
    Route::post('categories/delete', 'APICategoriesController@delete')->name('categories.delete');
});
?>