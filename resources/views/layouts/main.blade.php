<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', config('app.name'))</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link rel="preconnect" href="https://fonts.googleapis.com">

  <!-- DataTables -->
  <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">

  <!-- Vite -->
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

  <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">

    <!-- Linha do logo -->
    <div class="flex items-center justify-between mb-4 border-b border-gray-300 pb-2">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
      <span class="font-bold text-lg">BibliON</span>
    </div>
    @if (Route::has('login'))
    <nav class="flex items-center justify-end gap-4">
      {{-- Links fixos --}}
      <a href="/livros/create" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#19140035] rounded-sm text-[#1b1b18] text-sm leading-normal">Livros</a>
      <a href="/autores/create" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#19140035] rounded-sm text-[#1b1b18] text-sm leading-normal">Autores</a>
      <a href="/editoras/create" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#19140035] rounded-sm text-[#1b1b18] text-sm leading-normal">Editoras</a>

      {{-- Login / Register --}}
      @auth
      <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] rounded-sm text-sm leading-normal">Dashboard</a>
      @else
      <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal">Log in</a>

      @if (Route::has('register'))
      <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] rounded-sm text-sm leading-normal">Register</a>
      @endif
      @endauth
    </nav>
    @endif
  </header>

  <main class="w-full">
    <div class="max-w-screen-xl mx-auto px-4">
      <div class="flex flex-wrap">
        @if(session('msg'))
        <p class="w-full bg-green-100 text-green-800 border border-green-300 text-center px-4 py-2 mb-0">
          {{ session('msg') }}
        </p>
        @endif
        @yield('content')
      </div>
    </div>
  </main>


  @if (Route::has('login'))
  <div class="h-14.5 hidden lg:block"></div>
  @endif


  <footer>BiblyON &copy; 2025</footer>


  <!-- DataTables-->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('js/datatables.min.js') }}"></script>

  <script>
  $(document).ready(function() {
    $('.myTable').DataTable({
      select: true
    });
  });
  
</script>

</body>

</html>