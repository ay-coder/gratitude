<?php

Route::group([
    "namespace"  => "Notes",
], function () {
    /*
     * Admin Notes Controller
     */

    // Route for Ajax DataTable
    Route::get("notes/get", "AdminNotesController@getTableData")->name("notes.get-list-data");

    Route::resource("notes", "AdminNotesController");
});