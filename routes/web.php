<?php

use App\Livewire\PreRegister;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
//Route::get('pre-register', function(){
//    return view('pre-register-landing');
//});
Route::get('pre-register',\App\Livewire\Prospects\CreateProspects::class);
