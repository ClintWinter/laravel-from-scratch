<?php

Route::get('/tasks', 'TaskController@index');
Route::post('/tasks', 'TaskController@store');
Route::get('/tasks/create', 'TaskController@create');
Route::get('/tasks/{task}', 'TaskController@show');