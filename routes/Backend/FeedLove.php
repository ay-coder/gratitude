<?php

Route::group([
    "namespace"  => "FeedLove",
], function () {
    /*
     * Admin FeedLove Controller
     */

    // Route for Ajax DataTable
    Route::get("feedlove/get", "AdminFeedLoveController@getTableData")->name("feedlove.get-list-data");

    Route::resource("feedlove", "AdminFeedLoveController");
});