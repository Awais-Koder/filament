<?php

namespace App\Filament\Resources\CityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use App\Models\State;
use App\Models\City;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
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
                        ->relationship(name: 'department', titleAttribute: 'name')
                        ->native(false)
                        ->searchable()
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
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
                ->relationship('department' , 'name'),
                SelectFilter::make('Country')
                ->searchable()
                ->preload()
                ->native(false)
                ->indicator('Country')
                ->label('Filter by Country')
                ->relationship('country' , 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
