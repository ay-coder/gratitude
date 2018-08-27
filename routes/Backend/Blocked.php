<?php

Route::group([
    "namespace"  => "Blocked",
], function () {
    /*
     * Admin Blocked Controller
     */

    // Route for Ajax DataTable
    Route::get("blocked/get", "AdminBlockedController@getTableData")->name("blocked.get-list-data");

    Route::resource("blocked", "AdminBlockedController");
});