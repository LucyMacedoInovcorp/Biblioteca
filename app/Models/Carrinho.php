<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{
    protected $table = 'carrinhos';
    protected $fillable = [
        'user_id',
        'status', 
    ];

    // Relacionamentos
    public function itens()
    {
        return $this->hasMany(ItemCarrinho::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function encomenda()
    {
        return $this->hasOne(Encomenda::class);
    }

}
