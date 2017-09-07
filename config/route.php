<?php
use core\Route;
Route::get('/','IndexController@index');
Route::get('page/(:num)','IndexController@index');
Route::get('article/(:num)','IndexController@article');
Route::get('archives','ArchivesController@index');
Route::get('tags','TagsController@index');
Route::get('tags/(:any)','TagsController@index');
Route::get('about','IndexController@about');
Route::get('search/(:any)','IndexController@search');
Route::get('cat/(:num)','CatController@index');

Route::get('login','LoginController@login');
Route::post('update','AdminController@update');
Route::get('admin','AdminController@index');
Route::post('admin','AdminController@index');
Route::get('admin/add','AdminController@add');
Route::post('admin/add','AdminController@add');
Route::get('admin/(:num)/edit','AdminController@edit');
Route::post('admin/(:num)/edit','AdminController@edit');
Route::get('admin/(:num)/state','AdminController@state');
Route::dispatch();