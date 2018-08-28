<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('notes', 'APINotesController@index')->name('notes.index');
    Route::post('notes/create', 'APINotesController@create')->name('notes.create');
    Route::post('notes/edit', 'APINotesController@edit')->name('notes.edit');
    Route::post('notes/show', 'APINotesController@show')->name('notes.show');
    Route::post('notes/delete', 'APINotesController@delete')->name('notes.delete');
});
?>