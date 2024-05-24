<?php

use App\Http\Controllers\InvoicePdfController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(Filament::getUrl());
});

Route::get('download/{id}', [InvoicePdfController::class, 'invoicepdf'])->name("download.pdf");
