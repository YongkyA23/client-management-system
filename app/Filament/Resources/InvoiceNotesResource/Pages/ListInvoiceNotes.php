<?php

namespace App\Filament\Resources\InvoiceNotesResource\Pages;

use App\Filament\Resources\InvoiceNotesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceNotes extends ListRecords
{
    protected static string $resource = InvoiceNotesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
