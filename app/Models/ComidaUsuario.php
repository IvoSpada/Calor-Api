<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaUsuario extends Model
{
    use HasFactory;

    protected $table = 'comida_usuario';
    public $timestamps = false;

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

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function comidaDieta()
    {
        return $this->belongsTo(ComidaDieta::class, 'comida_dieta_id');
    }
}