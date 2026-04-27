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
        'stock_minimo',
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function editorial()
    {
        return $this->belongsTo(Editorial::class, 'editorial_id');
    }

    public function idioma()
    {
        return $this->belongsTo(Idioma::class, 'idioma_id');
    }

    public function formato()
    {
        return $this->belongsTo(Formato::class, 'formato_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'edicion_id');
    }

    public function promociones()
    {
        return $this->belongsToMany(Promocion::class, 'asigna_promociones', 'edicion_id', 'promocion_id')
                    ->withTimestamps();
    }

    public function detallesCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'edicion_id');
    }
}