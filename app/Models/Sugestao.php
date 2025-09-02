<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sugestao extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     * Correspondem às colunas da sua tabela `sugestoes`.
     *
     * @var array<int, string>
     */
    protected $table = 'sugestoes';
    protected $fillable = [
        'nome',
        'autor',
        'isbn',
        'bibliografia',
        'editora',
        'imagemcapa',
        'user_id',
        'status',
    ];

    /**
     * Define a relação: uma sugestão pertence a um utilizador.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}