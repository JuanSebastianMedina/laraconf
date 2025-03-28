<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'leader' => 'Liderazgo',
        'charisma' => 'Carisma',
        'humanitarian' => 'Trabajo humanitario',
        'contributor' => 'contribuyente',
        'influencer' => 'Influencer',
        'open_source' => 'Open Source'
    ];

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'email',
        'bio',
        'twitter_handle',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
                ->avatar()
                ->imageEditor()
                ->maxSize(1024 * 1024 * 10),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            //extInput::make('bio')
            RichEditor::make('bio')
                // ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                // ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options( self::QUALIFICATIONS)
                ->descriptions([
                    'leader' => 'Descata como lider',
                    'charisma' => 'Destaca por su carisma',
                    'humanitarian' => 'Reconocido por su trabajo humanitario',
                    'contributor' => 'Buen contribuyente',
                    'influencer' => 'Influencer de Youtube',
                    'open_source' => 'Activista',
                ])
                ->columns(3)
        ];
    }
}
