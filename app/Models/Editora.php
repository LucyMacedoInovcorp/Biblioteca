<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editora extends Model
{
    protected $table = 'editoras'; //Para não pegar o nome pré definido em inglês
    protected $fillable = ['nome', 'logotipo'];
}
