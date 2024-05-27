<?php

namespace App\Filament\Resources\InvoiceNotesResource\Pages;

use App\Filament\Resources\InvoiceNotesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceNotes extends EditRecord
{
    protected static string $resource = InvoiceNotesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
