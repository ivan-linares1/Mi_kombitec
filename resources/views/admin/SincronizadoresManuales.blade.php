@extends('layouts.app')

@section('title', 'Panel de Sincronizadores')

@section('contenido')
@vite(['resources/css/sincronizadores.css'])

<div class="container mt-5 sincronizadores-panel">
    <h1>Panel de Control de Sincronizadores Manuales</h1>
    <x-loading /> <!--animacion de cargando-->

    <div class="row g-4 justify-content-center">

        @php
            $cards = [
                [
                    'icon'     => 'bi-people-fill icon-clientes',
                    'titulo'   => 'Lista de Clientes',
                    'texto'    => 'Sincroniza la lista de Clientes.',
                    'servicio' => 'Clientes',
                    'ruta1'    => 'C:/SFTP_MiKombitec/data/Clientes/Clientes.xml',
                    'ruta2'    => '#',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
        <div class="col-md-3 col-sm-6">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi {{ $card['icon'] }}"></i>
                    <h5 class="card-title">{{ $card['titulo'] }}</h5>
                    <p class="card-text">{{ $card['texto'] }}</p>

                    <div class="d-flex gap-2">
                        <!-- BOTÓN 1 -->
                        <form action="{{ route('SincronizarArchivo', ['servicio' => $card['servicio']]) }}" method="POST" class="w-50">
                            @csrf
                            <input type="hidden" name="ruta" value="{{ $card['ruta1'] }}">
                            <button type="submit" class="btn btn-success w-100">Carga Total</button>
                        </form>

                        <!-- BOTÓN 2 -->
                        <form action="{{ route('SincronizarArchivo', ['servicio' => $card['servicio']]) }}" method="POST" class="w-50">
                            @csrf
                            <input type="hidden" name="ruta" value="{{ $card['ruta2'] }}">
                            <button type="submit" class="btn btn-success w-100">Carga Diaria</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection
