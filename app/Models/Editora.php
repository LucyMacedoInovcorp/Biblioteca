<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Editora extends Model
{
    use HasFactory;

    protected $table = 'editoras';
    protected $fillable = ['nome', 'logotipo'];

    // Relação: uma Editora tem muitos Livros
    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }
}
