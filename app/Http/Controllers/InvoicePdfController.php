<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceNotes;
use App\Models\User;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use PDF;

class InvoicePdfController extends Controller
{
    public function invoicepdf($id)
    {
        $invoice = Invoice::find($id);

        $invoiceNotes = InvoiceNotes::all()->first();

        $cPerson = User::find($invoice->cPerson_id);

        $data = [
            'invoice' => $invoice,
            'invoiceNotes' => $invoiceNotes,
            'cPerson' => $cPerson
        ];

        $pdf = PDF::loadView('invoice', $data);

        return $pdf->stream('invoice.pdf');
    }

    public function quotationpdf($id)
    {
        $invoice = Invoice::find($id);

        $invoiceNotes = InvoiceNotes::all()->first();

        $cPerson = User::find($invoice->cPerson_id);

        $data = [
            'invoice' => $invoice,
            'invoiceNotes' => $invoiceNotes,
            'cPerson' => $cPerson
        ];

        $pdf = PDF::loadView('quotation', $data);

        return $pdf->stream('quotation.pdf');
    }
}
