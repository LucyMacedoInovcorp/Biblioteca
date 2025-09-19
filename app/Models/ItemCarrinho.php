<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\EnviarCarrinhoAbandonadoJob;

class ItemCarrinho extends Model
{
    protected $table = 'item_carrinhos';

    protected $fillable = [
        'carrinho_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }

    /**
     * Boot model events.
     */
    protected static function booted()
    {
        static::created(function ($itemCarrinho) {
            // Dispara o job para o carrinho relacionado
            EnviarCarrinhoAbandonadoJob::dispatch($itemCarrinho->carrinho);
        });
    }
}
