<?php

namespace App\Models;
//Para clacular os 5 dias após a requisição
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    protected $table = 'requisicoes'; 

    protected $fillable = ['user_id', 'livro_id', 'ativo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }


    //Para clacular os 5 dias após a requisição
    public function getDataFimAttribute(){
    return Carbon::parse($this->created_at)->addDays(5);
}
}
