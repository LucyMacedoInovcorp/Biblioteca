<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_hora',
        'user_id',
        'modulo',
        'objeto_id',
        'alteracao',
        'ip',
        'browser',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar($modulo, $objetoId, $alteracao)
    {
        self::create([
            'data_hora' => now(),
            'user_id' => Auth::id(),
            'modulo' => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => $alteracao,
            'ip' => request()->ip(),
            'browser' => request()->userAgent(),
        ]);
    }
}