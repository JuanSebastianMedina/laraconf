<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

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

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options([
                    'leader' => 'Liderazgo',
                    'charisma' => 'Carisma',
                    'humanitarian' => 'Trabajo humanitario',
                    'contributor' => 'contribuyente',
                    'influencer' => 'Influencer',
                    'open_source' => 'Open Source'
                ])
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
