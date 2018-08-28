<?php

Route::group([
    "namespace"  => "FeedLike",
], function () {
    /*
     * Admin FeedLike Controller
     */

    // Route for Ajax DataTable
    Route::get("feedlike/get", "AdminFeedLikeController@getTableData")->name("feedlike.get-list-data");

    Route::resource("feedlike", "AdminFeedLikeController");
});