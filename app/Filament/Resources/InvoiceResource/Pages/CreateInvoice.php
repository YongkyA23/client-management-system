<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
