<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario'; // porque no es plural
    public $timestamps = false; // 👈 evita el error


    protected $fillable = [
        'nombre',
        'email',
        'peso',
        'altura',
        'edad',
        'objetivo',
        'password'
    ];

    // Un usuario puede tener muchas dietas
    public function dietas()
    {
        return $this->hasMany(Dieta::class, 'usuario_id');
    }

    // Un usuario puede tener muchas comidas registradas
    public function comidasUsuario()
    {
        return $this->hasMany(ComidaUsuario::class, 'usuario_id');
    }
}