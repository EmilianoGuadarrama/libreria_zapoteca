<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use SoftDeletes;

    protected $table = 'libros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'titulo',
        'sinopsis',
        'clasificacion_id',
        'anio_publicacion_original',
        'genero_principal_id'
    ];

    public function clasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'clasificacion_id');
    }

    public function ediciones()
    {
        return $this->hasMany(Edicion::class, 'libro_id');
    }
    public function autores()
{
    return $this->belongsToMany(Autor::class, 'asigna_autores', 'libro_id', 'autor_id')
                ->withTimestamps();
}
}