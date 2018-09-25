<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('report-feeds', 'APIReportFeedsController@index')->name('reportfeeds.index');

    Route::post('report-feeds/create', 'APIReportFeedsController@create')->name('reportfeeds.create');
    
    Route::post('report-feeds/edit', 'APIReportFeedsController@edit')->name('reportfeeds.edit');
    Route::post('report-feeds/show', 'APIReportFeedsController@show')->name('reportfeeds.show');
    Route::post('report-feeds/delete', 'APIReportFeedsController@delete')->name('reportfeeds.delete');
});
?>