<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livro extends Model
{
    protected $table = 'livros';
    protected $fillable = [
        'nome',
        'ISBN',
        'bibliografia',
        'preco',
        'editora_id',
        'imagemcapa'
    ];


    /**
     * Um Livro pertence a uma Editora
     */
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class, 'editora_id', 'id');
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }

    /*--------------------REQUISIÇÕES--------------------*/
    public function requisicoes()
    {
        return $this->hasMany(\App\Models\Requisicao::class);
    }

    public function getDisponivelAttribute()
    {
        return !$this->requisicoes()->where('ativo', true)->exists();
    }

    // Relação com Avaliações
    public function avaliacoes()
    {
        return $this->hasMany(\App\Models\Avaliacao::class, 'livro_id');
    }
}
