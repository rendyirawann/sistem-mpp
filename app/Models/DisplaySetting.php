<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplaySetting extends Model
{
    use HasFactory;

    protected $table = 'display_settings';

    protected $fillable = [
        'video_youtube_id',
        'ticker_text',
    ];
}
