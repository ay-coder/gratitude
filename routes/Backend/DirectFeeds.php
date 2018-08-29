<?php

Route::group([
    "namespace"  => "DirectFeeds",
], function () {
    /*
     * Admin DirectFeeds Controller
     */

    // Route for Ajax DataTable
    Route::get("directfeeds/get", "AdminDirectFeedsController@getTableData")->name("directfeeds.get-list-data");

    Route::resource("directfeeds", "AdminDirectFeedsController");
});