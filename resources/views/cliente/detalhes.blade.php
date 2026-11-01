@extends('layout.principal')

@section('titulo', 'Detalhes do Veículo - Locar')

@section('conteudo')

    <h2>Detalhes do veículo</h2>

    @if(isset($veiculo))

        <div class="detalhes-veiculo">

            <h3>{{ $veiculo->brand }} {{ $veiculo->model }}</h3>

            <p>Ano: {{ $veiculo->model_year }}</p>

            <p>Combustível: {{ $veiculo->fuel }}</p>

            <p>Diária: R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</p>

            <button type="button">Solicitar locação</button>

        </div>

    @else

        <p>Nenhum veículo selecionado.</p>

    @endif

    <p>
        <a href="{{ route('veiculos') }}">Voltar para veículos</a>
    </p>

@endsection
