<?php

namespace App\Models;

use App\Enums\Region;
use App\Models\Speaker;
use App\Models\Talk;
use App\Models\Venue;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get as FormsGet;
use Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Actions\Star;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Tabs\Tab;

class Conference extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'region',
        'venue_id',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            //Tabs::make()
            //->columnSpanFull()
            //->tabs([
            //    Tabs\Tab::make('Detalles de conferencia')
            //    ->schema([

            //        ]),
            //    Tabs\Tab::make('Ubicaciones')

            Section::make('Detalles de la conferencia')
            ->columns(2)
            //->aside()
            ->collapsible()
            ->description('Estos son los detalles de la conferencia')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('name')
                    ->label(label: 'Conferencia')
                    ->columnSpanFull()
                    ->required()
                    ->default( state: 'Mi conferencia')
                    ->maxLength(60),
                MarkdownEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->required(),
                DateTimePicker::make('start_date')
                    ->native( condition: false)
                    ->required(),
                DateTimePicker::make('end_date')
                    ->native( condition: false)
                    ->required(),
                Fieldset::make('estado')
                    ->columns(1)
                    ->schema([
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'archived' => 'Archived',
                        ])
                        ->required(),
                    Toggle::make( name: 'is_published') // interruptor
                        ->default( state: true),
                    ]),
            ]),

            Section::make('Ubicación')
            ->columns(2)
            ->schema([
                Select::make('region')
                    ->live()
                    ->enum( Region::class)
                    ->options( Region::class),
                Select::make('venue_id')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(Venue::getForm())
                    ->editOptionForm(Venue::getForm())
                    ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, FormsGet $get){
                        return $query->where('region', $get('region'));
                    }),
            ]),
            // CheckboxList::make('speakers')->relationship('speakers', 'name')->options(Speaker::all()->pluck('name', 'id'))->required()->columnSpanFull()->columns(3),
            Actions::make([
                Action::make('star')
                    ->label('Llenar con fabrica de datos')
                    ->icon('heroicon-m-star')
                    ->visible(function (string $operation)
                    {
                        if ($operation != 'create' ) {
                            return false;
                        }
                        if(! app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ]),
        ];
    }
}
