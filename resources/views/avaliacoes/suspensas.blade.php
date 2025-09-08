@extends('layouts.main')
@section('title', 'Avaliações Suspensas')
@section('content')
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">📝 Avaliações Suspensas</h2>
    @if(session('success'))
    <div class="mb-4" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.75rem 1rem; border-radius: 0.375rem;">
        {{ session('success') }}
    </div>
    @endif
    <table id="tabelaSuspensas" class="myTable table table-zebra w-full">
        <thead>
            <tr>
                <th>Cidadão</th>
                <th>Livro</th>
                <th>Nota</th>
                <th>Avaliação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($avaliacoes as $avaliacao)
            <tr>
                <td>{{ $avaliacao->user->name ?? '-' }}</td>
                <td>{{ $avaliacao->livro->nome ?? '-' }}</td>
                <td>{{ $avaliacao->rating }}</td>
                <td>{{ $avaliacao->review }}</td>
                <td>
                    <div class="flex gap-2">
                        <form action="{{ route('avaliacoes.suspensas.aprovar', $avaliacao->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Ativar</button>
                        </form>
                        <button type="button" class="btn btn-error btn-sm" onclick="toggleRecusa('{{ $avaliacao->id }}')">Recusar</button>
                    </div>
                    <div id="recusa-{{ $avaliacao->id }}" class="hidden mt-2">
                        <form action="{{ route('avaliacoes.suspensas.recusar', $avaliacao->id) }}" method="POST" class="flex flex-col gap-2 bg-red-50 p-4 rounded shadow">
                            @csrf
                            <label for="justificativa_recusa_{{ $avaliacao->id }}" class="font-semibold text-red-700 mb-2">Justificativa da recusa:</label>
                            <textarea id="justificativa_recusa_{{ $avaliacao->id }}" name="justificativa_recusa" rows="3" class="textarea textarea-bordered w-full" placeholder="Digite a justificativa" required></textarea>
                            <div class="flex justify-end gap-2 mt-2">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleRecusa('{{ $avaliacao->id }}')">Cancelar</button>
                                <button type="submit" class="btn btn-error btn-sm">Confirmar Recusa</button>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            {{-- Nenhuma linha, tbody fica vazio --}}
            @endforelse
            </tbody>
        </table>
        @if($avaliacoes->isEmpty())
            <div class="text-center py-6 text-gray-500">Nenhuma avaliação suspensa.</div>
        @endif
        </tbody>
    </table>


    <script>
        function toggleRecusa(id) {
            const box = document.getElementById('recusa-' + id);
            if (box.classList.contains('hidden')) {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
        }
        $(document).ready(function() {
            $('#tabelaSuspensas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
        });
    </script>

    @endsection