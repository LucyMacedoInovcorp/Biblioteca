<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
