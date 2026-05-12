<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clasificacion extends Model
{
    use SoftDeletes;

    protected $table = 'clasificaciones';

    protected $primaryKey = 'id';
    protected $fillable = ['nombre'];

    public function libros()
    {
        return $this->hasMany(Libro::class, 'clasificacion_id');
    }
}
