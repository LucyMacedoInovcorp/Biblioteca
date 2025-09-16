<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', config('app.name'))</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">

  <!-- DataTables -->
  <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
  <!-- DataTables Botões-->
  <link href="https://cdn.datatables.net/buttons/3.2.4/css/buttons.dataTables.min.css" rel="stylesheet" integrity="sha384-CEGjJEAUOa45Jz9PQsvKuOUrsy4B/1nuiBPCJWCO8JkPbziwbjZV+vqPsHc1wA7z" crossorigin="anonymous">

  <!-- Vite -->
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <!-- Dausy UI -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


  <style>
    * {
      font-family: Roboto;
    }

    .myTable {
      margin-top: 1.5rem !important;
      margin-bottom: 1.5rem !important;
    }

    .text-stroke {
      text-shadow:
        -1px -1px 0 #1e3a8a,
        1px -1px 0 #1e3a8a,
        -1px 1px 0 #1e3a8a,
        1px 1px 0 #1e3a8a;
      margin-bottom: 1.5rem;
    }    
  </style>

</head>

<body class="bg-base-100 text-base-content min-h-screen flex flex-col">

  <!-- NAVBAR -->
  <header class="navbar bg-base-200 shadow-md text-blue-900">
    <div class="flex-1">
      <a href="/" class="flex items-center gap-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
        <span class="font-bold text-lg">BibliON</span>
      </a>
    </div>
    <div class="flex-none gap-2">
      <ul class="menu menu-horizontal px-1">
        <li><a href="/livros/create">Livros</a></li>
        <li><a href="/autores/create">Autores</a></li>
        <li><a href="/editoras/create">Editoras</a></li>
        @auth
        <li><a href="{{ route('requisicoes.index') }}">✅ Requisições</a></li>
        <li><a href="{{ route('profile.show') }}">👤 Perfil</a></li>
        <li><a href="{{ route('carrinho.listar') }}">🛒 Carrinho</a></li>
        <li><a href="{{ route('encomendas.meus') }}">📦 Meus Pedidos</a></li>
        @if (auth()->user()->is_admin)
        <li><a href="{{ route('admin.dashboard') }}">⚙️ Administrador</a></li>
        @endif
        @endauth



        @auth
        <li class="nav-item">
          <form action="/logout" method="POST">
            @csrf
            <a href="/logout"
              class="nav-link"
              onclick="event.preventDefault();
                    this.closest('form').submit();">
              Sair
            </a>
          </form>
        </li>
        @endauth
        @guest
        <li class="nav-item">
          <a href="/login" class="nav-link">Entrar</a>
        </li>
        <li class="nav-item">
          <a href="/register" class="nav-link">Registar</a>
        </li>
        @endguest



      </ul>
    </div>
  </header>

  <!-- CONTEÚDO -->
  <main class="flex-1 container mx-auto p-6">
    @if(session('msg'))
    <div class="alert alert-success shadow-lg mb-4">
      <span>{{ session('msg') }}</span>
    </div>
    @endif
    @yield('content')
  </main>

  @if (Route::has('login'))
  <div class="h-14.5 hidden lg:block"></div>
  @endif


  <!-- FOOTER -->
  <footer class="footer footer-center p-4 bg-base-200 text-base-content">
    <aside>
      <p>BibliON &copy; 2025</p>
    </aside>
  </footer>


  <!-- Dependências do DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="{{ asset('js/datatables.min.js') }}"></script>

  <!-- Extensões dos botões -->
  <script src="https://cdn.datatables.net/buttons/3.2.4/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.4/js/buttons.print.min.js"></script>


  <script>
    $(document).ready(function() {
      var table = $('.myTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
        extend: 'excelHtml5',
        text: 'Exportar Excel'
      }],
      order: [['desc' ]],
      language: {
        search: 'Pesquisar:',
        lengthMenu: 'Mostrar _MENU_ registos',
        zeroRecords: 'Nada encontrado',
        info: 'Mostrando _START_-_END_ de _TOTAL_',
        infoEmpty: 'Sem registos',
        infoFiltered: '(filtrado de _MAX_)',
        paginate: {
        first: 'Primeiro',
        last: 'Último',
        next: 'Próximo',
        previous: 'Anterior'
        }
      }
      });

      //Estilização DaisyUI/Tailwind nos controles do DataTables 
      var $wrapper = $(table.table().container());

      // Campo de pesquisa
      $wrapper.find('div.dataTables_filter input')
        .addClass('input input-bordered w-64');

      // Select "Show N entries"
      $wrapper.find('div.dataTables_length select')
        .addClass('select select-bordered');

      // Botões (Excel, etc.)
      $wrapper.find('.dt-buttons .dt-button')
        .addClass('btn btn-secondary btn-sm mb-4 border border-base-300 shadow-sm hover:border-base-400 hover:bg-base-200')
        .removeClass('dt-button');




      // Paginação
      $wrapper.find('.dataTables_paginate')
        .addClass('join');
      $wrapper.find('.dataTables_paginate .paginate_button')
        .addClass('btn btn-sm join-item')
        .css({
          border: 'none',
          background: 'transparent'
        });

      // Info
      $wrapper.find('div.dataTables_info')
        .addClass('text-sm opacity-70');

      //  Preenche o select com os nomes das colunas 
      table.columns().every(function() {
        var column = this;
        var headerText = $(column.header()).text().trim();
        $('#colSelect').append(
          $('<option>', {
            value: column.index()
          }).text(headerText)
        );
      });

      // Filtro de colunas 
      $('#colSelect').on('change', function() {
        var val = $(this).val();
        if (val === "all") {
          table.columns().visible(true);
        } else {
          table.columns().visible(false);
          table.column(val).visible(true);
        }
      });
    });
  </script>





</body>

</html>