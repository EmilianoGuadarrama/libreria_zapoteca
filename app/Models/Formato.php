<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formato extends Model
{
    use SoftDeletes;

    protected $table = 'formatos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function ediciones()
    {
        return $this->hasMany(Edicion::class, 'formato_id');
    }
}