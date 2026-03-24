<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\SoftDeletes;
class Pais extends Model
{
    use softDeletes;

    protected $table = 'paises';

    protected $primaryKey = 'id';
    protected $fillable = ['nombre'];

}
