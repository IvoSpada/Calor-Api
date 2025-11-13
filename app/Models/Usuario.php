<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 👈 importante para login
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuario'; // 👈 sigue apuntando a tu tabla
    public $timestamps = false; // 👈 ya que no usás created_at/updated_at

    protected $fillable = [
        'nombre',
        'email',
        'peso',
        'altura',
        'edad',
        'objetivo',
        'password',
        'genero', // Añadido genero que estaba en el controlador pero no aquí
        
        // --- NUEVOS CAMPOS AÑADIDOS ---
        'patologias',
        'ejercicio',
        'premium',
    ];

    protected $hidden = [
        'password', // 👈 para que no aparezca en respuestas JSON
    ];

    // Relación: un usuario puede tener muchas dietas
    public function dietas()
    {
        return $this->hasMany(Dieta::class, 'usuario_id');
    }

    // Relación: un usuario puede tener muchas comidas registradas
    public function comidasUsuario()
    {
        return $this->hasMany(ComidaUsuario::class, 'usuario_id');
    }
}