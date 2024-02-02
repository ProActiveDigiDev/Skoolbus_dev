<?php

use App\Livewire\Rider\RiderRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Filament\Admin\Pages\RideRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//redirect from the home toe the busstop dashboard
Route::redirect('/', '/Busstop');

//login route for user panel
Route::get('/user-login', function () {
    return redirect(route('filament.Busstop.auth.login'));
})->name('user-login');

//login route for admin panel
Route::get('/admin-login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('admin-login');


//rider ride request page
Route::get('/admin/ride-request/{rider}', RideRequest::class)->name('rider-ride-request');

//rider ride request livewire page
Route::get('/livewire/rider/rider-request/{rider}', RiderRequest::class)->name('static-rider-ride-request');

