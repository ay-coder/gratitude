<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('comments', 'APICommentsController@index')->name('comments.index');
    Route::post('comments/create', 'APICommentsController@create')->name('comments.create');
    Route::post('comments/edit', 'APICommentsController@edit')->name('comments.edit');
    Route::post('comments/show', 'APICommentsController@show')->name('comments.show');
    Route::post('comments/delete', 'APICommentsController@delete')->name('comments.delete');
    Route::post('comments/blocked', 'APICommentsController@blocked')->name('comments.blocked');
});
?>