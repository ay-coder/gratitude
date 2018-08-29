<?php

Route::group([
    "namespace"  => "DirectFeedImages",
], function () {
    /*
     * Admin DirectFeedImages Controller
     */

    // Route for Ajax DataTable
    Route::get("directfeedimages/get", "AdminDirectFeedImagesController@getTableData")->name("directfeedimages.get-list-data");

    Route::resource("directfeedimages", "AdminDirectFeedImagesController");
});