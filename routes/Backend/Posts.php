<?php

Route::group([
    "namespace"  => "Posts",
], function () {
    /*
     * Admin Posts Controller
     */

    // Route for Ajax DataTable
    Route::get("posts/get", "AdminPostsController@getTableData")->name("posts.get-list-data");

    Route::resource("posts", "AdminPostsController");
});