<?php

Route::group([
    "namespace"  => "Comments",
], function () {
    /*
     * Admin Comments Controller
     */

    // Route for Ajax DataTable
    Route::get("comments/get", "AdminCommentsController@getTableData")->name("comments.get-list-data");

    Route::resource("comments", "AdminCommentsController");
});