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
    *{
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
      -1px  1px 0 #1e3a8a,
       1px  1px 0 #1e3a8a;
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
        <li><a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline">Dashboard</a></li>
        @else
        <li><a href="{{ route('login') }}">Login</a></li>
        @if (Route::has('register'))
        <li><a href="{{ route('register') }}">Register</a></li>
        @endif
        @endauth
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
      });

      // Preenche o select com os nomes das colunas
      table.columns().every(function() {
        var column = this;
        var headerText = $(column.header()).text();
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