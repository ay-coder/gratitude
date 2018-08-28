<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('reportcomments', 'APIReportCommentsController@index')->name('reportcomments.index');
    Route::post('reportcomments/create', 'APIReportCommentsController@create')->name('reportcomments.create');
    Route::post('reportcomments/edit', 'APIReportCommentsController@edit')->name('reportcomments.edit');
    Route::post('reportcomments/show', 'APIReportCommentsController@show')->name('reportcomments.show');
    Route::post('reportcomments/delete', 'APIReportCommentsController@delete')->name('reportcomments.delete');
});
?>