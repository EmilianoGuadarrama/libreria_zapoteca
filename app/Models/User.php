<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'persona_id',
        'correo',
        'contrasena',
        'estado',
        'rol_id'
        
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id');
    }
    public function persona()
{
    return $this->belongsTo(Persona::class, 'persona_id');
}
}
