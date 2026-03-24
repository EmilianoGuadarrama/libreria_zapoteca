<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autores'; 

    protected $fillable = [
        'persona_id',
        'nacionalidad_id',
        'biografia'
    ];

    
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }
    public function libros()
{
    return $this->belongsToMany(Libro::class, 'asigna_autores', 'autor_id', 'libro_id')
                ->withTimestamps();
}
}