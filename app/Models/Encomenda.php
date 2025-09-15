<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    protected $table = 'encomendas';
    protected $fillable = [
        'user_id',
        'carrinho_id',
        'total',
        'morada_entrega',
        'status',
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
