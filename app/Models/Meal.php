<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'image', 'ingredients', 'steps', 'duration', 'complexity', 'affordability',
        'isGlutenFree', 'isLactoseFree', 'isVegan', 'isVegetarian'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
        'isGlutenFree' => 'boolean',
        'isLactoseFree' => 'boolean',
        'isVegan' => 'boolean',
        'isVegetarian' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
