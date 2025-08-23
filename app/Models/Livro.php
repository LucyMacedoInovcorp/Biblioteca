<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livro extends Model
{
    protected $table = 'livros'; // opcional, se o nome da tabela for exatamente esse
    protected $fillable = ['titulo', 'editora_id'];

    /**
     * Um Livro pertence a uma Editora
     */
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class, 'editora_id', 'id');
    }
}
