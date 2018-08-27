<?php

Route::group([
    "namespace"  => "Students",
], function () {
    /*
     * Admin Students Controller
     */

    // Route for Ajax DataTable
    Route::get("students/get", "AdminStudentsController@getTableData")->name("students.get-list-data");

    Route::resource("students", "AdminStudentsController");
});