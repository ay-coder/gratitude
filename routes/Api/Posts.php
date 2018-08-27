<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('posts', 'APIPostsController@index')->name('posts.index');
    Route::post('posts-filter', 'APIPostsController@postFilter')->name('posts.posts-filter');
    Route::any('my-posts', 'APIPostsController@my')->name('posts.my-posts');
    Route::post('posts/create', 'APIPostsController@create')->name('posts.create');
    Route::post('posts/edit', 'APIPostsController@edit')->name('posts.edit');
    Route::post('posts/show', 'APIPostsController@show')->name('posts.show');
    Route::post('posts/delete', 'APIPostsController@delete')->name('posts.delete');

    Route::get('posts/list', 'APIPostsController@list')->name('posts.list');
    Route::post('posts/accept', 'APIPostsController@accept')->name('posts.accept');
    Route::post('posts/reject', 'APIPostsController@reject')->name('posts.reject');
});
?>