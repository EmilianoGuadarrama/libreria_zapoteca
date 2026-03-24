<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignaAutor extends Model
{
    use SoftDeletes;

    protected $table = 'asigna_autores';

    protected $fillable = [
        'libro_id',
        'autor_id'
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function autor()
    {
        return $this->belongsTo(Autor::class, 'autor_id');
    }
}
