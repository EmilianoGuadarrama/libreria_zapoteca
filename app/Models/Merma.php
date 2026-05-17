<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merma extends Model
{
    use SoftDeletes;

    // Tabla en Oracle
    protected $table = 'mermas';
    protected $primaryKey = 'id';

    // Campos asignables
    protected $fillable = [
        'lote_id',
        'tipo_merma',
        'fecha_reporte',
        'cantidad',
        'usuario_id',
        'destino',
        'estatus',
    ];

    // Cast de fechas
    protected $casts = [
        'fecha_reporte' => 'datetime',
    ];

    // ──────────────────────────────────────────────
    // RELACIONES
    // ──────────────────────────────────────────────

    /**
     * Lote al que pertenece la merma
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    /**
     * Usuario que reportó la merma
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ──────────────────────────────────────────────
    // ACCESSORS
    // ──────────────────────────────────────────────

    public function getPrecioUnitarioAttribute()
    {
        return $this->lote && $this->lote->edicion
            ? (float) $this->lote->edicion->precio_venta
            : 0;
    }

    public function getTotalMermaAttribute()
    {
        return abs((int) $this->cantidad) * $this->precio_unitario;
    }

    public function getMontoRecuperadoAttribute()
    {
        return $this->destino === 'Devolucion_Proveedor'
            ? $this->total_merma
            : 0;
    }

    public function getMontoPerdidoAttribute()
    {
        return $this->destino === 'Destruccion'
            ? $this->total_merma
            : 0;
    }
}
