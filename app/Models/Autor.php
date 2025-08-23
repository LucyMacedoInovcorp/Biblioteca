<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'autores'; //ra não pegar pre definido e usar em rtuguês
    protected $fillable = ['nome', 'foto'];

        public function livros()
    {
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }
}
