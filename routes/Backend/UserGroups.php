<?php

Route::group([
    "namespace"  => "UserGroups",
], function () {
    /*
     * Admin UserGroups Controller
     */

    // Route for Ajax DataTable
    Route::get("usergroups/get", "AdminUserGroupsController@getTableData")->name("usergroups.get-list-data");

    Route::resource("usergroups", "AdminUserGroupsController");
});