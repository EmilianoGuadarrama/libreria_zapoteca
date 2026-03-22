<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'folio',
        'usuario_id',
        'fecha',
        'total',
        'monto_recibido',
        'cambio'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}