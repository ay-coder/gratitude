<?php

Route::group([
    "namespace"  => "ReportComments",
], function () {
    /*
     * Admin ReportComments Controller
     */

    // Route for Ajax DataTable
    Route::get("reportcomments/get", "AdminReportCommentsController@getTableData")->name("reportcomments.get-list-data");

    Route::resource("reportcomments", "AdminReportCommentsController");
});