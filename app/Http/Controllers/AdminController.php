<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Para o administrador confirmar a receção
use App\Models\Requisicao;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }


    //Para o administrador confirmar a receção
    public function confirmarRecepcao($id)
{
    $req = Requisicao::findOrFail($id);

    // Marca data real de recepção
    $req->data_recepcao = now();
    $req->ativo = false; // encerra a requisição
    $req->save();

    // Atualiza livro para disponível (se tiver essa coluna no model Livro)
    if ($req->livro) {
        $req->livro->disponivel = true;
        $req->livro->save();
    }

    return back()->with('success', '✅ Devolução confirmada, livro disponível novamente.');
}
}
