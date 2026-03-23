<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ubicacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'pasillo',
        'estante',
        'nivel',
        'genero_id'
    ];

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'ubicacion_id');
    }
}