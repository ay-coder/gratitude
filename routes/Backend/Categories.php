<?php

Route::group([
    "namespace"  => "Categories",
], function () {
    /*
     * Admin Categories Controller
     */

    // Route for Ajax DataTable
    Route::get("categories/get", "AdminCategoriesController@getTableData")->name("categories.get-list-data");

    Route::resource("categories", "AdminCategoriesController");
});