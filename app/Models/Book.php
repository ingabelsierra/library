<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Autor;

class Book extends Model
{

     protected $fillable = [
        
        'title','isbn','cover_large', 'created_at', 'updated_at'
    ];

    public function autors()
    {
        return $this->hasMany(Autor::class);
    }   
}
