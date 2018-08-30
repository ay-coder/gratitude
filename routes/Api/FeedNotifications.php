<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('feednotifications', 'APIFeedNotificationsController@index')->name('feednotifications.index');

    Route::post('my-notifications', 'APIFeedNotificationsController@getAllNotifications')->name('feednotifications.get-all');

    Route::post('notifications/clear-all', 'APIFeedNotificationsController@clearAll')->name('feednotifications.clear-all');


    Route::post('feednotifications/create', 'APIFeedNotificationsController@create')->name('feednotifications.create');
    Route::post('feednotifications/edit', 'APIFeedNotificationsController@edit')->name('feednotifications.edit');
    Route::post('feednotifications/show', 'APIFeedNotificationsController@show')->name('feednotifications.show');
    Route::post('feednotifications/delete', 'APIFeedNotificationsController@delete')->name('feednotifications.delete');
});
?>