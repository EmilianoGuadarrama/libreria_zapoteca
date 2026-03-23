<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use SoftDeletes;

    protected $table = 'lotes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'compra_id',
        'edicion_id',
        'codigo',
        'fecha_entrada',
        'cantidad',
        'usuario_id',
        'ubicacion_id'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function edicion()
    {
        return $this->belongsTo(Edicion::class, 'edicion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'lote_id');
    }
}