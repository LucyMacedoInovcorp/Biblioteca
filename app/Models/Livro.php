<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livro extends Model
{
    use HasFactory;
    
    protected $table = 'livros';
    protected $fillable = [
        'nome',
        'ISBN',
        'bibliografia',
        'preco',
        'editora_id',
        'imagemcapa',
        'stripe_product_id',
        'stripe_price_id',
        'estoque',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer'
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
        return !$this->requisicoes()->where('ativo', true)->exists() && $this->temEstoque();
    }

    // Relação com Avaliações
    public function avaliacoes()
    {
        return $this->hasMany(\App\Models\Avaliacao::class, 'livro_id');
    }

    // Relação com Notificações de Disponibilidade
    public function notificacoesDisponibilidade()
    {
        return $this->hasMany(NotificacaoDisponibilidade::class);
    }

    // Método para obter livros relacionados com base na bibliografia FULLTEXT MYSQL
    public function relacionados($limit = 5)
    {
        return Livro::where('id', '!=', $this->id)
            ->whereRaw("MATCH(bibliografia) AGAINST(? IN NATURAL LANGUAGE MODE)", [$this->bibliografia])
            ->limit($limit)
            ->get();
    }

    /*--------------------ESTOQUE--------------------*/
    
    /**
     * Verifica se o livro tem estoque disponível
     *
     * @param int $quantidade Quantidade desejada (padrão: 1)
     * @return bool
     */
    public function temEstoque(int $quantidade = 1): bool
    {
        return $this->estoque >= $quantidade;
    }

    /**
     * Reduz o estoque do livro
     *
     * @param int $quantidade Quantidade a reduzir (padrão: 1)
     * @return bool Sucesso da operação
     */
    public function reduzirEstoque(int $quantidade = 1): bool
    {
        if (!$this->temEstoque($quantidade)) {
            return false;
        }

        $this->decrement('estoque', $quantidade);
        return true;
    }

    /**
     * Aumenta o estoque do livro
     *
     * @param int $quantidade Quantidade a adicionar (padrão: 1)
     * @return bool
     */
    public function adicionarEstoque(int $quantidade = 1): bool
    {
        $this->increment('estoque', $quantidade);
        return true;
    }

    /**
     * Verifica se o livro está em falta (sem estoque)
     *
     * @return bool
     */
    public function emFalta(): bool
    {
        return $this->estoque <= 0;
    }

    /**
     * Verifica se o estoque está baixo (menos que o limite especificado)
     *
     * @param int $limite Limite para considerar estoque baixo (padrão: 5)
     * @return bool
     */
    public function estoqueBaixo(int $limite = 5): bool
    {
        return $this->estoque > 0 && $this->estoque <= $limite;
    }
}