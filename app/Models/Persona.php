<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use softDeletes;
    protected $table = 'personas';
    protected $primaryKey = 'id_persona';
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'no_telefono'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class, 'persona_id', 'id_persona');
    }
}
