<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('readpost', 'APIReadPostController@index')->name('readpost.index');
    Route::post('readpost/create', 'APIReadPostController@create')->name('readpost.create');
    Route::post('readpost/edit', 'APIReadPostController@edit')->name('readpost.edit');
    Route::post('readpost/show', 'APIReadPostController@show')->name('readpost.show');
    Route::post('readpost/delete', 'APIReadPostController@delete')->name('readpost.delete');
});
?>