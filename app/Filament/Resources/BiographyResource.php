<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BiographyResource\Pages;
use App\Models\Biography;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\AiBiographyService;

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
                    Group::make()->columnSpan(2)->schema([
                        Section::make('Content')->schema([
                            Tabs::make('Biography Content')->tabs([
                                Tabs\Tab::make('SEO & Hero')
                                    ->icon('heroicon-o-globe-alt')
                                    ->schema([
                                        TextInput::make('full_name')->required()->maxLength(255),
                                        TextInput::make('slug')->required()->maxLength(255),
                                        TextInput::make('content_data.seo.metaTitle')->label('Meta Title')->maxLength(255),
                                        Textarea::make('content_data.seo.metaDescription')->label('Meta Description')->rows(3)->maxLength(500),
                                        TextInput::make('content_data.hero.h1Title')->label('H1 Title')->maxLength(255),
                                        
                                        RichEditor::make('content_data.hero.intro')->label('Intro Paragraph')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                        Textarea::make('source_for_intro')->label('Source Text for Intro Regeneration')->helperText('Paste text here and click regenerate.')->rows(4),
                                        Actions::make([
                                            Action::make('regenerate_intro')
                                                ->label('Regenerate Intro')
                                                ->icon('heroicon-o-sparkles')
                                                ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                    self::regenerateSection($set, $get, $aiService, $arguments);
                                                })
                                                ->arguments(['sectionKey' => 'Introduction', 'statePath' => 'content_data.hero.intro', 'sourcePath' => 'source_for_intro']),
                                        ])->alignEnd(),
                                    ]),

                                Tabs\Tab::make('Main Biography')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Section::make('Trending Focus')->schema([
                                            TextInput::make('content_data.trendingFocus.title')->label('Section Title')->default('Aktuelle Entwicklungen'),
                                            RichEditor::make('content_data.trendingFocus.content')->label('Content')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                            Textarea::make('source_for_trending')->label('Source Text for Trending Focus')->rows(5),
                                            Actions::make([
                                                Action::make('regenerate_trending')
                                                    ->label('Regenerate Section')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                        self::regenerateSection($set, $get, $aiService, $arguments);
                                                    })
                                                    ->arguments(['sectionKey' => 'Trending Focus', 'statePath' => 'content_data.trendingFocus.content', 'sourcePath' => 'source_for_trending']),
                                            ])->alignEnd(),
                                        ])->collapsible(),

                                        Section::make('Early Life')->schema([
                                            TextInput::make('content_data.earlyLife.title')->label('Section Title')->default('Frühe Jahre'),
                                            RichEditor::make('content_data.earlyLife.content')->label('Content')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                            Textarea::make('source_for_earlylife')->label('Source Text for Early Life')->rows(5),
                                            Actions::make([
                                                Action::make('regenerate_earlylife')
                                                    ->label('Regenerate Section')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                        self::regenerateSection($set, $get, $aiService, $arguments);
                                                    })
                                                    ->arguments(['sectionKey' => 'Early Life', 'statePath' => 'content_data.earlyLife.content', 'sourcePath' => 'source_for_earlylife']),
                                            ])->alignEnd(),
                                        ])->collapsible(),

                                        Section::make('Personal Life')->schema([
                                            TextInput::make('content_data.personalLife.title')->label('Section Title')->default('Privatleben'),
                                            RichEditor::make('content_data.personalLife.content')->label('Content')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                            Textarea::make('source_for_personallife')->label('Source Text for Personal Life')->rows(5),
                                            Actions::make([
                                                Action::make('regenerate_personallife')
                                                    ->label('Regenerate Section')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                        self::regenerateSection($set, $get, $aiService, $arguments);
                                                    })
                                                    ->arguments(['sectionKey' => 'Personal Life', 'statePath' => 'content_data.personalLife.content', 'sourcePath' => 'source_for_personallife']),
                                            ])->alignEnd(),
                                        ])->collapsible(),

                                        Section::make('Net Worth')->schema([
                                            TextInput::make('content_data.netWorth.title')->label('Section Title')->default('Vermögen'),
                                            RichEditor::make('content_data.netWorth.content')->label('Content')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                            Textarea::make('source_for_networth')->label('Source Text for Net Worth')->rows(5),
                                            Actions::make([
                                                Action::make('regenerate_networth')
                                                    ->label('Regenerate Section')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                        self::regenerateSection($set, $get, $aiService, $arguments);
                                                    })
                                                    ->arguments(['sectionKey' => 'Net Worth', 'statePath' => 'content_data.netWorth.content', 'sourcePath' => 'source_for_networth']),
                                            ])->alignEnd(),
                                        ])->collapsible(),

                                        Section::make('Death')->schema([
                                            TextInput::make('content_data.death.title')->label('Section Title')->default('Tod'),
                                            RichEditor::make('content_data.death.content')->label('Content')->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList']),
                                            Textarea::make('source_for_death')->label('Source Text for Death Section')->rows(5),
                                            Actions::make([
                                                Action::make('regenerate_death')
                                                    ->label('Regenerate Section')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->action(function (callable $set, callable $get, AiBiographyService $aiService, array $arguments) {
                                                        self::regenerateSection($set, $get, $aiService, $arguments);
                                                    })
                                                    ->arguments(['sectionKey' => 'Death', 'statePath' => 'content_data.death.content', 'sourcePath' => 'source_for_death']),
                                            ])->alignEnd(),
                                        ])->collapsible(),
                                    ]),
                                
                                // ... Other tabs remain unchanged ...
                                Tabs\Tab::make('Career Details')->icon('heroicon-o-briefcase')->schema([/* ... your existing code ... */]),
                                Tabs\Tab::make('FAQs & Sources')->icon('heroicon-o-question-mark-circle')->schema([/* ... your existing code ... */]),
                            ]),
                        ]),
                    ]),

                    // Sidebar column
                    Group::make()->columnSpan(1)->schema([
                        Section::make('Quick Facts (Infobox)')->schema([
                            TextInput::make('content_data.quickFacts.title')
                                ->label('Infobox Title')
                                ->default('Kurzbiografie auf einen Blick'),

                            Repeater::make('content_data.quickFacts.facts')
                                ->label('Facts')
                                ->schema([
                                    TextInput::make('label')
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('value')
                                        ->required()
                                        ->maxLength(255),
                                ])
                                ->columns(1)
                                ->collapsible(),
                        ]),

                        Section::make('Image Details')->schema([
                            TextInput::make('content_data.hero.mainImageUrl')
                                ->label('Main Image URL')
                                ->url()
                                ->maxLength(500),
                            TextInput::make('content_data.hero.imageAltText')
                                ->label('Image Alt Text')
                                ->maxLength(255),
                            TextInput::make('content_data.hero.imageCaption')
                                ->label('Image Caption')
                                ->maxLength(500),
                            TextInput::make('content_data.hero.mainImageCredit')
                                ->label('Image Credit')
                                ->maxLength(255),
                            TextInput::make('content_data.hero.mainImageLicense')
                                ->label('Image License')
                                ->maxLength(255),
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
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('M j, Y H:i')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBiographies::route('/'),
            'create' => Pages\CreateBiography::route('/create'),
            'edit' => Pages\EditBiography::route('/{record}/edit'),
        ];
    }

    // Add your regenerateSection method here
    public static function regenerateSection(callable $set, callable $get, AiBiographyService $aiService, array $arguments): void
{
    $sectionKey = $arguments['sectionKey'];
    $statePath = $arguments['statePath'];
    $sourcePath = $arguments['sourcePath'];

    $personName = $get('full_name');
    $sourceText = $get($sourcePath);
    $oldContent = $get($statePath);

    if (empty($personName)) {
        Notification::make()->title('Person Name Required')->body('Please enter the full name before regenerating.')->warning()->send();
        return;
    }

    if (empty($sourceText)) {
        Notification::make()->title('Source Text Required')->body("Please provide source text in the '{$sourcePath}' field to regenerate this section.")->warning()->send();
        return;
    }

    Notification::make()->title('Regenerating section...')->body("Sending request to AI for '{$sectionKey}' section.")->info()->send();
    try {
        // ########## THE FIX IS HERE ##########
        // Change "generateBiographySection" to the correct method name "regenerateSection"
        $newContent = $aiService->regenerateSection($personName, $sectionKey, $sourceText, $oldContent);
        // #####################################

        $set($statePath, $newContent);
        Notification::make()->title('Section Regenerated Successfully')->success()->send();
    } catch (\Exception $e) {
        Notification::make()->title('AI Regeneration Failed')->body($e->getMessage())->danger()->send();
    }
}
}
