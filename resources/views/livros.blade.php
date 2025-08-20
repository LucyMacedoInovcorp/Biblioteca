@extends('layouts.main')

@section('title', 'BibliON')

@section('content')



@if($busca != '')
<p>O usuário está buscando por {{$busca}}</p>
@endif

@endsection