<?php

Route::group([
    "namespace"  => "FeedNotifications",
], function () {
    /*
     * Admin FeedNotifications Controller
     */

    // Route for Ajax DataTable
    Route::get("feednotifications/get", "AdminFeedNotificationsController@getTableData")->name("feednotifications.get-list-data");

    Route::resource("feednotifications", "AdminFeedNotificationsController");
});