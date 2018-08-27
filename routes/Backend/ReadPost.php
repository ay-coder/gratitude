<?php

Route::group([
    "namespace"  => "ReadPost",
], function () {
    /*
     * Admin ReadPost Controller
     */

    // Route for Ajax DataTable
    Route::get("readpost/get", "AdminReadPostController@getTableData")->name("readpost.get-list-data");

    Route::resource("readpost", "AdminReadPostController");
});