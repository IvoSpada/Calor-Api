<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dieta extends Model
{
    use HasFactory;

    protected $table = 'dieta';
    public $timestamps = false;


    protected $fillable = [
        'usuario_id',
        'fecha_inicio',
        'fecha_fin',
        'origen',
        'estado',
    ];

    // Relación: una dieta pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación: una dieta tiene muchas comidas planificadas
    public function comidasDieta()
    {
        return $this->hasMany(ComidaDieta::class, 'dieta_id');
    }
}