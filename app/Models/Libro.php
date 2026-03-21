<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class libro extends Model
{
    //
    use softDeletes;
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
        return $this->belongsTo(clasificacion::class, 'clasificacion_id');
    }
}
