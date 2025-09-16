<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    public function itens()
    {
        return $this->hasMany(ItemEncomenda::class);
    }
    protected $table = 'encomendas';

    protected $fillable = [
        'carrinho_id',
        'user_id',
        'status',
        'total',
        'logradouro',
        'numero',
        'porta',
        'localidade',
        'codigo_postal',
        'concelho',
        'pais',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }
}
