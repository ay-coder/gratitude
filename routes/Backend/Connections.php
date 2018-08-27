<?php

Route::group([
    "namespace"  => "Connections",
], function () {
    /*
     * Admin Connections Controller
     */

    // Route for Ajax DataTable
    Route::get("connections/get", "AdminConnectionsController@getTableData")->name("connections.get-list-data");

    Route::resource("connections", "AdminConnectionsController");
});