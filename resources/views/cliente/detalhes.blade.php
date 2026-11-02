@extends('layout.principal')

@section('titulo', 'Detalhes do Veículo - Locar')

@section('conteudo')

    <h2>Detalhes do Veículo</h2>

    <div class="detalhes-veiculo">

        <h3>{{ $veiculo->brand }} {{ $veiculo->model }}</h3>

        <p>Ano: {{ $veiculo->model_year }}</p>

        <p>Combustível: {{ $veiculo->fuel }}</p>

        <p>Diária: R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</p>

    </div>

    <p>
        <a href="{{ route('veiculos') }}">
            Voltar para Veículos
        </a>
    </p>

@endsection