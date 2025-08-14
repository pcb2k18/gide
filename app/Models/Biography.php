<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biography extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This is a security feature to prevent unwanted data submission.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'full_name',
        'status',
        'content_data',
    ];

    /**
     * The attributes that should be cast to native types.
     * This automatically handles the JSON column for us.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content_data' => 'array',
    ];
}
