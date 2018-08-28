<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('usergroupmembers', 'APIUserGroupMembersController@index')->name('usergroupmembers.index');
    Route::post('usergroupmembers/create', 'APIUserGroupMembersController@create')->name('usergroupmembers.create');
    Route::post('usergroupmembers/edit', 'APIUserGroupMembersController@edit')->name('usergroupmembers.edit');
    Route::post('usergroupmembers/show', 'APIUserGroupMembersController@show')->name('usergroupmembers.show');
    Route::post('usergroupmembers/delete', 'APIUserGroupMembersController@delete')->name('usergroupmembers.delete');
});
?>