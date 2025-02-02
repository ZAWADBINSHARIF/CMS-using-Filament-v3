<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\AuthorRelationManager;
use App\Models\Post;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                            Select::make("category_id")
                                ->rules(['required'])->required()
                                ->relationship('category', "name"),
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
                    ]),

                    Section::make("Author")->schema([
                        Select::make("author")
                            ->relationship("author", "name")
                            ->searchable()
                            ->multiple()
                    ])
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Filter::make('published')->query(fn(Builder $query) => $query->where('published', true)),
                SelectFilter::make('tags')->relationship('category', 'name')->searchable()->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            AuthorRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
