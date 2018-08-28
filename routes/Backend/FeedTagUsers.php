<?php

Route::group([
    "namespace"  => "FeedTagUsers",
], function () {
    /*
     * Admin FeedTagUsers Controller
     */

    // Route for Ajax DataTable
    Route::get("feedtagusers/get", "AdminFeedTagUsersController@getTableData")->name("feedtagusers.get-list-data");

    Route::resource("feedtagusers", "AdminFeedTagUsersController");
});