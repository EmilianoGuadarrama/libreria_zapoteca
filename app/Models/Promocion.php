<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promocion extends Model
{
    //
    use SoftDeletes;

    protected $table = 'promociones';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_final',
        'autorizado_por_id',
        'porcentaje_descuento'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_final' => 'date'
    ];
}
