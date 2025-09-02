<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaDieta extends Model
{
    use HasFactory;

    protected $table = 'comida_dieta';
    public $timestamps = false;


    protected $fillable = [
        'dieta_id',
        'fecha',
        'tipo',
        'descripcion',
        'calorias',
        'proteinas',
        'carbohidratos',
        'grasas',
    ];

    // Relación: una comida pertenece a una dieta
    public function dieta()
    {
        return $this->belongsTo(Dieta::class, 'dieta_id');
    }

    // Relación: una comida planificada puede estar en comidas de usuario
    public function comidasUsuario()
    {
        return $this->hasMany(ComidaUsuario::class, 'comida_dieta_id');
    }
}