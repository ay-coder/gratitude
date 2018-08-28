<?php

Route::group([
    "namespace"  => "Feeds",
], function () {
    /*
     * Admin Feeds Controller
     */

    // Route for Ajax DataTable
    Route::get("feeds/get", "AdminFeedsController@getTableData")->name("feeds.get-list-data");

    Route::resource("feeds", "AdminFeedsController");
});