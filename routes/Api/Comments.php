<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('comments', 'APICommentsController@index')->name('comments.index');
    Route::post('comments/sendcomment', 'APICommentsController@create')->name('comments.create');

    Route::post('sendcomment', 'APICommentsController@sendcomment')->name('comments.sendcomment');

    Route::post('deletecomment', 'APICommentsController@deletecomment')->name('comments.deletecomment');

    Route::post('reportcomment', 'APICommentsController@reportcomment')->name('comments.reportcomment');

    Route::post('getcommentlist', 'APICommentsController@getList')->name('comments.get-list');

    
    Route::post('comments/edit', 'APICommentsController@edit')->name('comments.edit');

    
    Route::post('comments/show', 'APICommentsController@show')->name('comments.show');
    Route::post('comments/delete', 'APICommentsController@delete')->name('comments.delete');
});
?>