<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoothStaffViewController;


Route::get('/admin/booth-staff', [BoothStaffViewController::class, 'index']);
Route::get('/admin/booth-staff/create', [BoothStaffViewController::class, 'create']);
Route::post('/admin/booth-staff', [BoothStaffViewController::class, 'store']);
Route::get('/admin/booth-staff/{id}/edit', [BoothStaffViewController::class, 'edit']);
Route::post('/admin/booth-staff/{id}', [BoothStaffViewController::class, 'update']);
Route::delete('/admin/booth-staff/{id}', [BoothStaffViewController::class, 'destroy']);
