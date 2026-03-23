<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'proveedor_id',
        'folio_factura',
        'fecha_compra',
        'total_compra',
        'usuario_id',
        'estado'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'compra_id');
    }
}