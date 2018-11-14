<?php

Route::group([
    "namespace"  => "BlockUsers",
], function () {
    /*
     * Admin BlockUsers Controller
     */

    // Route for Ajax DataTable
    Route::get("blockusers/get", "AdminBlockUsersController@getTableData")->name("blockusers.get-list-data");

    Route::resource("blockusers", "AdminBlockUsersController");
});