<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Create a post")
                    ->description("Create a post over here")
                    ->schema(
                        [
                            TextInput::make("title")
                                ->rules(['min:3', 'required'])
                                ->required(),
                            TextInput::make("slug")
                                ->rules(['min:3', 'required'])
                                ->unique(ignoreRecord: true)
                                ->required(),
                            ColorPicker::make("color")->required(),
                            MarkdownEditor::make("content")
                                ->maxLength(1024)
                                ->columnSpanFull(),
                        ]
                    )->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make("Image")->schema([
                        FileUpload::make("thumbnail")
                            ->disk('public')->directory("posts"),
                    ])->collapsed(false),
                    Section::make("Meta")->schema([
                        TagsInput::make("tags"),
                        Checkbox::make('published')->default(false)
                    ])
                ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make("thumbnail")->toggleable(),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make("title")->sortable()->searchable(),
                TextColumn::make("category.name")->sortable()->searchable()->toggleable(),
                TextColumn::make("tags")->searchable()->toggleable(),
                CheckboxColumn::make("published")->toggleable(),
                TextColumn::make('created_at')
                    ->label("Created")
                    ->timezone("Asia/Dhaka")
                    ->date('d-M-y h:i A'),
                TextColumn::make('updated_at')
                    ->label("Updated")
                    ->timezone("Asia/Dhaka")
                    ->date('d-M-y h:i A')
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
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
}
