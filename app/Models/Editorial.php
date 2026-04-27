<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Editorial extends Model
{
    use SoftDeletes;

    protected $table = 'editoriales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'pais_id',
        'correo',
        'telefono',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function ediciones()
    {
        return $this->hasMany(Edicion::class, 'editorial_id');
    }
}