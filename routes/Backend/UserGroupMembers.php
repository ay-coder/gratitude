<?php

Route::group([
    "namespace"  => "UserGroupMembers",
], function () {
    /*
     * Admin UserGroupMembers Controller
     */

    // Route for Ajax DataTable
    Route::get("usergroupmembers/get", "AdminUserGroupMembersController@getTableData")->name("usergroupmembers.get-list-data");

    Route::resource("usergroupmembers", "AdminUserGroupMembersController");
});