<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos
    protected $fillable = ['name', 'description', 'type'];


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
