<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplayAnnouncement extends Model
{
    use HasFactory;

    protected $table = 'display_announcements';

    protected $fillable = [
        'title',
        'text',
        'order_index',
        'is_active',
    ];
}
