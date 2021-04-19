<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Autor extends Model
{
 
     protected $fillable = [
        
        'name', 'book_id' ,'created_at', 'updated_at'
    ]; 
    
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
