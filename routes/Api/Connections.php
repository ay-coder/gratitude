<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('connections', 'APIConnectionsController@index')->name('connections.index');

    Route::post('connections/search-global', 'APIConnectionsController@searchGlobal')->name('connections.search-global');

    Route::post('my-connections', 'APIConnectionsController@myConnections')->name('connections.my-connections');
    
    Route::post('connections-search', 'APIConnectionsController@search')->name('connections.search');

    Route::post('search-app-users', 'APIConnectionsController@searchAppUsers')->name('connections.search-app-users');


    Route::post('connections/create', 'APIConnectionsController@create')->name('connections.create');

    Route::post('connections/show-requests', 'APIConnectionsController@showRequests')->name('connections.show-requests');

    Route::post('connections/show-my-requests', 'APIConnectionsController@showMyRequests')->name('connections.show-my-requests');

    Route::post('connections/remove-my-request', 'APIConnectionsController@removeMyRequest')->name('connections.remove-my-request');

    Route::post('connections/request-accept', 'APIConnectionsController@acceptRequests')->name('connections.request-accept');

   	Route::post('connections/request-reject', 'APIConnectionsController@rejectRequests')->name('connections.request-reject');

    Route::post('connections/edit', 'APIConnectionsController@edit')->name('connections.edit');
    Route::post('connections/show', 'APIConnectionsController@show')->name('connections.show');
    Route::post('connections/delete', 'APIConnectionsController@delete')->name('connections.delete');

    Route::post('connections/block', 'APIConnectionsController@block')->name('connections.block');
});
?>