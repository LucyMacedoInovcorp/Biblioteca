<?php

namespace App\Models;

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

    // accessor: Para calcular os 5 dias após a requisição
    public function getDataFimAttribute()
    {
        return Carbon::parse($this->created_at)->addDays(5);
    }

    // accessor: Para calcular os dias decorridos (devolução)
    protected $dates = ['data_recepcao'];
    protected $casts = [
        'data_recepcao' => 'datetime',
    ];

    public function getDiasDecorridosAttribute()
    {
        $inicio = Carbon::parse($this->created_at);
        $fim = $this->data_recepcao ? Carbon::parse($this->data_recepcao) : now();
        return (int) $inicio->diffInDays($fim);
    }
}
