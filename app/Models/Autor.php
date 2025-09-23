<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autores'; //ra não pegar pre definido e usar em rtuguês
    protected $fillable = ['nome', 'foto'];

        public function livros()
    {
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }
}
