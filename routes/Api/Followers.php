<?php
Route::group(['namespace' => 'Api'], function()
{
	Route::post('followers/suggestions', 'APIFollowersController@getSuggestions')->name('followers.suggestions');

	Route::post('followers/add', 'APIFollowersController@addFollow')->name('followers.add');

	Route::post('followers/remove', 'APIFollowersController@removeFollow')->name('followers.remove');

    Route::post('my-followings', 'APIFollowersController@index')->name('followers.index');

    

    Route::post('followers/create', 'APIFollowersController@create')->name('followers.create');
    Route::post('followers/edit', 'APIFollowersController@edit')->name('followers.edit');
    Route::post('followers/show', 'APIFollowersController@show')->name('followers.show');
    Route::post('followers/delete', 'APIFollowersController@delete')->name('followers.delete');
});
?>