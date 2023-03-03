<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AbstractAPIModel;

class Book extends AbstractAPIModel
{
    use HasFactory;
    protected $fillable = ['publication_year', 'title', 'description'];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function type()
    {
        return 'books';
    }
}
