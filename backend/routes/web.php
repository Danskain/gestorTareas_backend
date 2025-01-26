<?php

use App\Events\PrivateChanelEvent;
use App\Events\PublicChanelEvent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    event(new PrivateChanelEvent('Hello World', 1));
    event(new PublicChanelEvent('Hello World'));
});
