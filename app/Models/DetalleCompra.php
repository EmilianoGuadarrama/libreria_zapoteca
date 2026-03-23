<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCompra extends Model
{
    use HasFactory, SoftDeletes;

    // Nombre exacto de la tabla en tu base de datos Oracle
    protected $table = 'detalles_compras';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'compra_id',
        'edicion_id',
        'cantidad',
        'subtotal'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function edicion()
    {
        return $this->belongsTo(Edicion::class, 'edicion_id');
    }
}