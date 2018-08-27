<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('students', 'APIStudentsController@index')->name('students.index');
    Route::post('students/create', 'APIStudentsController@create')->name('students.create');
    Route::post('students/edit', 'APIStudentsController@edit')->name('students.edit');
    Route::post('students/show', 'APIStudentsController@show')->name('students.show');
    Route::post('students/delete', 'APIStudentsController@delete')->name('students.delete');
});
?>