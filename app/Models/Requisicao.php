<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisicao extends Model
{
    use HasFactory;

    protected $table = 'requisicoes';

    protected $fillable = [
        'user_id',
        'livro_id',
        'ativo',
        'created_at',
        'updated_at',
        'data_recepcao',
    ];

    protected $dates = ['data_recepcao'];

    protected $casts = [
        'data_recepcao' => 'datetime',
    ];

    // Faz com que o campo 'prazo_devolucao' apareça ao serializar o modelo
    protected $appends = ['prazo_devolucao'];

    /*
     * Relações
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    /*
     * Accessors
     */

    // Prazo da devolução = created_at + 5 dias
    public function getPrazoDevolucaoAttribute()
    {
        return $this->created_at ? $this->created_at->copy()->addDays(5) : null;
    }

    // Dias decorridos entre created_at e data_recepcao (ou agora)
    public function getDiasDecorridosAttribute()
    {
        $inicio = Carbon::parse($this->created_at);
        $fim = $this->data_recepcao ? Carbon::parse($this->data_recepcao) : now();
        return (int) $inicio->diffInDays($fim);
    }
}
