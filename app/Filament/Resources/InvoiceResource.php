<?php

namespace App\Filament\Resources;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ServiceCategory;
use App\Models\services;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
    protected static ?string $modelLabel = 'Invoice and Quotations';

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
                                    ->label('Project Name')
                                    ->required()
                                    ->searchable()
                                    ->options(Project::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(
                                        function ($state, callable $get, callable $set) {
                                            $project = Project::find($state);
                                            if ($project) {
                                                $set('project_date', $project->start_date);
                                            }
                                        }
                                    )
                                    ->reactive(),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->label('Invoice Title')
                                    ->maxLength(255),
                                Forms\Components\Section::make('Price')
                                    ->schema([
                                        Forms\Components\TextInput::make('total')
                                            ->required()
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated(true)
                                            ->prefix('IDR')
                                            ->default(0),
                                        Forms\Components\TextInput::make('tax_percent')
                                            ->required()
                                            ->numeric()
                                            ->suffix('%')
                                            ->reactive()
                                            ->default(0)
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $totalSum = collect($get('invoice_details'))->sum('total_price');
                                                if ($state !== null) {
                                                    $totalWithTax = $totalSum + ($totalSum * $state / 100);
                                                    $set('total', $totalWithTax);
                                                }
                                            }),
                                    ])->columnSpanFull()
                                    ->columns(2),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Date')
                            ->schema([
                                Forms\Components\DatePicker::make('issue_date')
                                    ->required()
                                    ->afterOrEqual(fn (callable $get) => $get('project_date')),
                                Forms\Components\DatePicker::make('due_date')
                                    ->required()
                                    ->afterOrEqual('issue_date'),
                                Forms\Components\DatePicker::make('paid_date')
                                    ->nullable()
                                    ->afterOrEqual('issue_date'),
                                Forms\Components\DatePicker::make('project_date')
                                    ->hidden()
                                    ->dehydrated(false)
                            ])->columns('3'),
                        Forms\Components\Section::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                            ])
                    ]),
                Forms\Components\Section::make('Items')
                    ->schema([
                        Repeater::make('invoice_details')
                            ->relationship()
                            ->schema([
                                Select::make('service_category_id')
                                    ->required()
                                    ->searchable()
                                    ->label('Service Category')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                    ])
                                    ->createOptionUsing(function ($data) {
                                        return ServiceCategory::create($data)->id;
                                    })
                                    ->columnSpan(4)
                                    ->options(ServiceCategory::all()->pluck('name', 'id')),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->columnSpan(4)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $price = $get('price');
                                        $set('total_price', $state * $price);
                                    }),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required()
                                    ->default(0)
                                    ->reactive()
                                    ->columnSpan(3)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $quantity = $get('quantity');
                                        $set('total_price', $state * $quantity);
                                    }),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->numeric()
                                    ->disabled()
                                    ->default(0)
                                    ->columnSpan(3)
                                    ->prefix('IDR')
                                    ->required(),
                            ])->columnSpanFull()
                            ->columns(15)
                            ->addActionLabel('Add More Items')
                            ->defaultItems(1)
                            ->reorderable(true)
                            ->reorderableWithButtons()
                            ->cloneable()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $totalSum = collect($state)->sum('total_price');
                                $set('total', $totalSum);

                                $tax = $get('tax_percent');
                                if ($tax !== null) {
                                    $totalWithTax = $totalSum + ($totalSum * $tax / 100);
                                    $set('total', $totalWithTax);
                                }
                            })
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('paid_date')
                    ->boolean()
                    ->default(false)
                    ->label('Paid'),
                Tables\Columns\TextColumn::make('project.client.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
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
                    ->label("Invoice")
                    ->icon("heroicon-c-document-text")
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('Generate Quatation')
                    ->label("Quotation")
                    ->icon("heroicon-c-document-text")
                    ->url(fn (Invoice $record) => route('quotation.pdf', $record))
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
            //RelationManagers\InvoiceDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'activities' => Pages\ListInvoiceActivities::route('/{record}/activities'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
