<?php

Route::group([
    "namespace"  => "ReportFeeds",
], function () {
    /*
     * Admin ReportFeeds Controller
     */

    // Route for Ajax DataTable
    Route::get("reportfeeds/get", "AdminReportFeedsController@getTableData")->name("reportfeeds.get-list-data");

    Route::resource("reportfeeds", "AdminReportFeedsController");
});