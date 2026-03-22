<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'genero'
    ];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
}
