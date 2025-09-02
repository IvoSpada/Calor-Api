<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaUsuario extends Model
{
    use HasFactory;

    protected $table = 'comida_usuario';

    protected $fillable = [
        'usuario_id',
        'comida_dieta_id',
        'fecha',
        'opcion',
        'descripcion',
        'calorias',
        'proteinas',
        'carbohidratos',
        'grasas',
    ];

    // Relación: pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación: puede referenciar una comida planificada
    public function comidaDieta()
    {
        return $this->belongsTo(ComidaDieta::class, 'comida_dieta_id');
    }
}