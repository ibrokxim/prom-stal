<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

//Route::post('/', function (){
//    ini_set('memory_limit', '2G');
//
//    $file = request()->file('csv_file');
//
//    $fileContent = file_get_contents($file->getRealPath());
//
//    $utf8Content = mb_convert_encoding($fileContent, 'UTF-8', 'Windows-1251');
//
//    file_put_contents($file->getRealPath(), $utf8Content);
//
//    dd($utf8Content);
//});


Route::post('/import/stores', [FeedbackController::class, 'importFromCSV']);
