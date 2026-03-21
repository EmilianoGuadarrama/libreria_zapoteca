<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    //
    protected $table = 'clasificaciones';

    protected $primaryKey = 'id';
    protected $fillable = ['nombre'];
}
