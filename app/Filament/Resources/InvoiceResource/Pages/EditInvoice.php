<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Generate Invoice')
                ->label('Generate Invoice')
                ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                ->openUrlInNewTab(),
            Actions\Action::make('Generate Quotations')
                ->label('Generate Quotations')
                ->url(fn (Invoice $record) => route('quotation.pdf', $record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
