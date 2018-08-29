<?php

Route::group([
    "namespace"  => "Followers",
], function () {
    /*
     * Admin Followers Controller
     */

    // Route for Ajax DataTable
    Route::get("followers/get", "AdminFollowersController@getTableData")->name("followers.get-list-data");

    Route::resource("followers", "AdminFollowersController");
});