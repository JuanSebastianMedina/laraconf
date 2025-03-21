<?php

namespace App\Models;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Talk extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = [
        'title',
        'abstract',
        'speaker_id',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'id' => 'integer',
        'speaker_id' => 'integer',
        'status' => TalkStatus::class,
        'length' => TalkLength::class,
    ];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function approve(): void
    {
        $this->status = TalkStatus::APPROVED;
        //enviar correos u otras cosas
        $this->save();
    }

    public function reject(): void
    {
        $this->status = TalkStatus::REJECTED;
        //enviar correos u otras cosas
        $this->save();
    }

    public static function getForm($speakerId = null): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->label('Titulo')
                ->maxLength(255),
            RichEditor::make('abstract')
                ->label('Resumen')
                ->required()
                ->columnSpanFull(),
            Select::make('speaker_id')
                ->relationship('speaker', 'name')
                ->label('Ponente')
                ->required()
                ->hidden( function() use ($speakerId) {
                    return $speakerId !== null;
                }),
            Select::make('status')
                ->label('Estado')
                ->live()
                ->enum( TalkStatus::class)
                ->options( TalkStatus::class)
                ->required(),
        ];
    }
}
