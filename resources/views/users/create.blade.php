@extends('layouts.main')
@section('title', 'Users')
@section('content')

<div id="users-create-container" class="max-w-2xl mx-auto mt-10">
  <div class="card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">👥 Criar novo utilizador</h1>

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc ml-5">
          @foreach ($errors->all() as $error) 
            <li>{{ $error }}</li> 
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
      @csrf

      <!-- Nome -->
      <div class="form-control">
        <label for="name" class="label">
          <span class="label-text font-semibold">Nome</span>
        </label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" 
               placeholder="Digite o nome do utilizador"
               class="input input-bordered w-full" required />
      </div>

      <!-- Email -->
      <div class="form-control">
        <label for="email" class="label">
          <span class="label-text font-semibold">Email</span>
        </label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               placeholder="Digite o email"
               class="input input-bordered w-full" required />
      </div>

      <!-- Palavra-passe -->
      <div class="form-control">
        <label for="password" class="label">
          <span class="label-text font-semibold">Palavra-passe</span>
        </label>
        <input type="password" id="password" name="password"
               placeholder="Digite a palavra-passe"
               class="input input-bordered w-full" required />
      </div>

      <!-- Confirmar Palavra-passe -->
      <div class="form-control">
        <label for="password_confirmation" class="label">
          <span class="label-text font-semibold">Confirmar palavra-passe</span>
        </label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               placeholder="Confirme a palavra-passe"
               class="input input-bordered w-full" required />
      </div>

      <!-- Perfil -->
      <div class="form-control">
        <label for="is_admin" class="label">
          <span class="label-text font-semibold">Perfil</span>
        </label>
        <select id="is_admin" name="is_admin" class="select select-bordered w-full" required>
          <option disabled selected>Selecione o perfil</option>
          <option value="0" {{ old('is_admin')==='0' ? 'selected' : '' }}>👥 Cidadão</option>
          <option value="1" {{ old('is_admin')==='1' ? 'selected' : '' }}>🛠️ Administrador</option>
        </select>
      </div>

      <!-- Botão -->
      <div class="form-control mt-6">
        <button type="submit" class="btn btn-primary w-full">➕ Criar utilizador</button>
      </div>
    </form>
  </div>
</div>

@endsection