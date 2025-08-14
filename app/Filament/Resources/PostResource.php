<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Str;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true) // live update on blur
                ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            
            TextInput::make('author_name')
                ->label('Guest Author Name')
                ->maxLength(255),

            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ])
                ->required()
                ->default('draft'),

            FileUpload::make('featured_image')
                ->image()
                ->imageEditor(), // Adds a nice image editor

            Textarea::make('excerpt')
                ->rows(3)
                ->maxLength(500),

            RichEditor::make('content')
                ->required()
                ->columnSpanFull(),
        ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('featured_image')->label('Image'),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('author_name')->searchable()->sortable(),
            BadgeColumn::make('status')
                ->colors([
                    'warning' => 'draft',
                    'success' => 'published',
                ]),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])
        ->filters([
            //
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
            //
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
