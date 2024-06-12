<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers;
use App\Models\State;
use App\Models\City;
use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\SelectFilter;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Residence')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(
                                function (Set $set) {
                                    $set('city_id', null);
                                    $set('state_id', null);
                                }
                            )
                            ->required(),
                        Forms\Components\Select::make('state_id')
                            ->options(
                                fn (Get $get): Collection => State::query()
                                    ->where('country_id', $get('country_id'))
                                    ->pluck('name', 'id')
                            )
                            ->native(false)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->options(
                                fn (Get $get): Collection => City::query()
                                    ->where('state_id', $get('state_id'))
                                    ->pluck('name', 'id')
                            )
                            ->native(false)
                            ->live()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) =>
                                $query->whereBelongsTo(Filament::getTenant())
                            )
                            ->native(false)
                            ->required(),
                    ])->columns(2),
                Section::make('User Name')
                    ->description('Put the user name details in.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
                Section::make('User Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->autosize(),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false)
                            ->required()
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('date_hired')
                            ->native(false)
                            ->required()
                            ->displayFormat('d/m/Y'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
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
                SelectFilter::make('Department')
                    ->label('Filter by Department')
                    ->native(false)
                    ->indicator('Department')
                    ->relationship('department', 'name'),
                SelectFilter::make('Country')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->indicator('Country')
                    ->label('Filter by Country')
                    ->relationship('country', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Employee deleted successfully.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Relationships')
                    ->schema([
                        TextEntry::make('country.name'),
                        TextEntry::make('city.name'),
                        TextEntry::make('state.name'),
                        TextEntry::make('department.name'),
                    ])->columns(2),
                InfoSection::make('Name')
                    ->schema([
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('middle_name'),
                    ])->columns(3),
                InfoSection::make('User Address')
                    ->schema([
                        TextEntry::make('address'),
                        TextEntry::make('zip_code'),
                    ])->columns(2),
                InfoSection::make('User Address')
                    ->schema([
                        TextEntry::make('address'),
                        TextEntry::make('zip_code'),
                    ])->columns(2),
                InfoSection::make('Dates')
                    ->schema([
                        TextEntry::make('date_of_birth'),
                        TextEntry::make('date_hired'),
                    ])->columns(2),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
