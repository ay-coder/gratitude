<?php

Route::group([
    "namespace"  => "Templates",
], function () {
    /*
     * Admin Templates Controller
     */

    // Route for Ajax DataTable
    Route::get("templates/get", "AdminTemplatesController@getTableData")->name("templates.get-list-data");

    Route::resource("templates", "AdminTemplatesController");
});