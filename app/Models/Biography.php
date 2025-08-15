<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biography extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'full_name',
        'status',
        'content_data',
    ];

    protected $casts = [
        'content_data' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug'; // This tells Laravel to use the 'slug' field for route model binding
    }
}
