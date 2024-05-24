<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use PDF;

class InvoicePdfController extends Controller
{
    public function invoicepdf($id)
    {
        $invoice = Invoice::find($id);

        $data = [
            'invoice' => $invoice
        ];

        $pdf = PDF::loadView('invoice', $data);

        return $pdf->stream('invoice.pdf');
    }
}
