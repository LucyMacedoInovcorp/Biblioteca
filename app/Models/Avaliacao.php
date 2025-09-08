<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;
    protected $table = 'avaliacoes';
    protected $fillable = [
        'user_id',
        'livro_id',
        'requisicao_id',
        'review',
        'rating',
    'status', // 'suspenso', 'ativo', 'recusado'
    ];

    // Relacionamentos
    public function user() { return $this->belongsTo(User::class); }
    public function livro() { return $this->belongsTo(Livro::class); }
    public function requisicao() { return $this->belongsTo(Requisicao::class); }
}
