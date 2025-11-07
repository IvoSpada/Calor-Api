<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComidaUsuario extends Model
{
    use HasFactory;

    protected $table = 'comida_usuario';
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    // --- INICIO DE LA CORRECCIÓN ---
    // El array $fillable ahora coincide con tu migración y el validador
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
        // Asegúrate de que 'fecha_consumo' y 'cantidad' NO estén aquí
    ];
    // --- FIN DE LA CORRECCIÓN ---

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function comidaDieta()
    {
        return $this->belongsTo(ComidaDieta::class, 'comida_dieta_id');
    }
}