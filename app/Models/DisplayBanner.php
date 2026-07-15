<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisplayBanner extends Model
{
    use HasFactory;

    protected $table = 'display_banners';

    protected $fillable = [
        'image_path',
        'title',
        'order_index',
        'is_active',
    ];
}
