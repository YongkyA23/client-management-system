<?php

use App\Http\Controllers\InvoicePdfController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(Filament::getUrl());
});

Route::get('download/invoice/{id}', [InvoicePdfController::class, 'invoicepdf'])->name("invoice.pdf");
Route::get('download/quotation/{id}', [InvoicePdfController::class, 'quotationpdf'])->name("quotation.pdf");
