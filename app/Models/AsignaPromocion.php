<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsignaPromocion extends Model
{
    use softDeletes;

    protected $table = 'asigna_promociones';
    protected $primaryKey = 'id';
    protected $fillable = [
      'promocion_id',
      'edicion_id'
    ];

}
