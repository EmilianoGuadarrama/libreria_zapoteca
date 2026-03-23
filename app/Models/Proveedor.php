<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'persona_contacto_id',
        'correo',
        'telefono',
        'estado'
    ];

    public function personaContacto()
    {
        return $this->belongsTo(Persona::class, 'persona_contacto_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}