<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('user-groups', 'APIUserGroupsController@index')->name('usergroups.index');

    Route::post('user-group/create', 'APIUserGroupsController@create')->name('usergroups.create');

    Route::post('user-group/delete', 'APIUserGroupsController@delete')->name('usergroups.delete');

    /*Route::post('usergroups/create', 'APIUserGroupsController@create')->name('usergroups.create');*/
    Route::post('usergroups/edit', 'APIUserGroupsController@edit')->name('usergroups.edit');
    Route::post('usergroups/show', 'APIUserGroupsController@show')->name('usergroups.show');
    /*Route::post('usergroups/delete', 'APIUserGroupsController@delete')->name('usergroups.delete');*/
});
?>