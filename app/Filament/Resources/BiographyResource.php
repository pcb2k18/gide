<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BiographyResource\Pages;
use App\Filament\Resources\BiographyResource\RelationManagers;
use App\Models\Biography;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;

class BiographyResource extends Resource
{
    protected static ?string $model = Biography::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Grid::make(3)->schema([
                // Main content column
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Content')->schema([
                            Tabs::make('Biography Content')->tabs([
                                Tabs\Tab::make('SEO & Hero')
                                    ->icon('heroicon-o-globe-alt')
                                    ->schema([
                                        TextInput::make('content_data.seo.metaTitle')->label('Meta Title'),
                                        Textarea::make('content_data.seo.metaDescription')->label('Meta Description')->rows(3),
                                        TextInput::make('content_data.hero.h1Title')->label('H1 Title'),
                                        RichEditor::make('content_data.hero.intro')->label('Intro Paragraph'),
                                    ]),

                                Tabs\Tab::make('Main Biography')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        RichEditor::make('content_data.trendingFocus.content')->label('Trending Focus Content'),
                                        RichEditor::make('content_data.earlyLife.content')->label('Early Life Content'),
                                        RichEditor::make('content_data.personalLife.content')->label('Personal Life Content'),
                                        RichEditor::make('content_data.netWorth.content')->label('Net Worth Content'),
                                        RichEditor::make('content_data.death.content')->label('Death Content'),
                                    ]),

                                // ########## START: CORRECTED CODE ##########
                                Tabs\Tab::make('Career Details')
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        Repeater::make('content_data.career.timeline')->label('Career Timeline')
                                            ->schema([
                                                TextInput::make('year')->required(),
                                                TextInput::make('event')->required(),
                                                Textarea::make('description')->rows(2)->columnSpanFull(),
                                            ])->columns(2),
                                        Repeater::make('content_data.career.sections')->label('Career Sub-sections')
                                            ->schema([
                                                TextInput::make('subtitle')->required(),
                                                RichEditor::make('content')->required()->columnSpanFull(),
                                            ]),
                                    ]),
                                
                                Tabs\Tab::make('FAQs & Sources')
                                    ->icon('heroicon-o-question-mark-circle')
                                    ->schema([
                                        Repeater::make('content_data.faqs.questions')->label('FAQs')
                                            ->schema([
                                                TextInput::make('question')->required(),
                                                RichEditor::make('answer')->required()->columnSpanFull(),
                                            ]),
                                        Repeater::make('content_data.sources.sourceList')->label('Sources')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                TextInput::make('url')->url()->required(),
                                                TextInput::make('publisher'),
                                                DatePicker::make('publishedDate'),
                                            ])->columns(2),
                                    ]),
                                // ########## END: CORRECTED CODE ##########
                            ]),
                        ]),
                    ]),

                // Sidebar column
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Details')->schema([
                            TextInput::make('full_name')->required(),
                            TextInput::make('slug')->required()->helperText('The unique URL.'),
                        ]),
                        
                        Section::make('Quick Facts (Infobox)')->schema([
                            Repeater::make('content_data.quickFacts.facts')->label('')
                                ->schema([
                                    TextInput::make('label')->required(),
                                    TextInput::make('value')->required(),
                                ])->columns(2),
                        ]),
                        
                        Section::make('Image Details')->schema([
                            TextInput::make('content_data.hero.mainImageUrl')->label('Main Image URL'),
                            TextInput::make('content_data.hero.imageAltText')->label('Image Alt Text'),
                            TextInput::make('content_data.hero.imageCaption')->label('Image Caption'),
                            TextInput::make('content_data.hero.mainImageCredit')->label('Image Credit'),
                            TextInput::make('content_data.hero.mainImageLicense')->label('Image License'),
                        ]),
                    ]),
            ]),
        ]);
}
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('full_name')
                ->label('Full Name')
                ->searchable()
                ->sortable(),

            BadgeColumn::make('status')
                ->colors([
                    'warning' => 'under_review',
                    'success' => 'reviewed',
                ])
                ->sortable(),
            
            TextColumn::make('slug')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true), // Hide by default for a cleaner look

            TextColumn::make('updated_at')
                ->dateTime('M j, Y H:i') // Format the date for readability
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'under_review' => 'Under Review',
                    'reviewed' => 'Reviewed',
                ]),
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
            'index' => Pages\ListBiographies::route('/'),
            'create' => Pages\CreateBiography::route('/create'),
            'edit' => Pages\EditBiography::route('/{record}/edit'),
        ];
    }
}
