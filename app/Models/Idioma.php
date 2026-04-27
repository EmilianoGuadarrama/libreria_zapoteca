<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idioma extends Model
{
    use SoftDeletes;

    protected $table = 'idiomas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
    ];

    public function ediciones()
    {
        return $this->hasMany(Edicion::class, 'idioma_id');
    }
}