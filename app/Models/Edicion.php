<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edicion extends Model
{
    use SoftDeletes;

    protected $table = 'ediciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'libro_id',
        'editorial_id',
        'idioma_id',
        'formato_id',
        'isbn',
        'anio_publicacion',
        'numero_edicion',
        'numero_paginas',
        'precio_venta',
        'portada',
        'alt_imagen',
        'existencias',
        'stock_minimo'
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'edicion_id');
    }
}