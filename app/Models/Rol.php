<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps = true;

    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id');
    }
}
