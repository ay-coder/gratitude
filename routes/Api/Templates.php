<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('templates', 'APITemplatesController@index')->name('templates.index');
    Route::post('templates/create', 'APITemplatesController@create')->name('templates.create');
    Route::post('templates/edit', 'APITemplatesController@edit')->name('templates.edit');
    Route::post('templates/show', 'APITemplatesController@show')->name('templates.show');
    Route::post('templates/delete', 'APITemplatesController@delete')->name('templates.delete');
});
?>