<?php

Route::group([
    "namespace"  => "FeedImages",
], function () {
    /*
     * Admin FeedImages Controller
     */

    // Route for Ajax DataTable
    Route::get("feedimages/get", "AdminFeedImagesController@getTableData")->name("feedimages.get-list-data");

    Route::resource("feedimages", "AdminFeedImagesController");
});