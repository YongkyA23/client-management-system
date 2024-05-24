<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Projects";
    protected static ?int $navigationSort = 3;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Details')
                            ->schema([
                                Forms\Components\Select::make('project_id')
                                    ->required()
                                    ->searchable()
                                    ->relationship(name: 'project', titleAttribute: 'name'),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('detail')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('total')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR'),

                            ]),
                        Forms\Components\Section::make('Notes')
                            ->schema([
                                Forms\Components\TextInput::make('notes')
                                    ->maxLength(255),
                            ]),

                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Date')
                            ->schema([
                                Forms\Components\DatePicker::make('issue_date')
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date')
                                    ->required(),
                                Forms\Components\DatePicker::make('paid_date'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('paid_date')
                    ->boolean()
                    ->label('Paid'),
                Tables\Columns\TextColumn::make('project.client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Generate invoice')
                    ->label("Generate Invoice")
                    ->icon("heroicon-c-document-text")
                    ->url(fn (Invoice $record) => route('download.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoiceDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
