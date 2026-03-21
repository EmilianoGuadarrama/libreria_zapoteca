<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clasificacion extends Model
{
    //
    use softDeletes;

    protected $table = 'clasificaciones';

    protected $primaryKey = 'id';
    protected $fillable = ['nombre'];
}
