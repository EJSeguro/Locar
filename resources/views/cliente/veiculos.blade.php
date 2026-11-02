@extends('layout.principal')

@section('titulo', 'Veículos - Locar')

@section('conteudo')

    <h2>Veículos Disponíveis</h2>

    <p>Confira os veículos disponíveis para locação.</p>

    <div class="lista-veiculos">

        @forelse($veiculos ?? [] as $veiculo)
            <div class="veiculo">
                <h3>{{ $veiculo->brand }} {{ $veiculo->model }}</h3>

                <p>Ano: {{ $veiculo->model_year }}</p>

                <p>Combustível: {{ $veiculo->fuel }}</p>

                <p>Diária: R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</p>

                <a href="{{ url('/veiculos/' . $veiculo->id) }}" class="botao-detalhes">
                    Ver Detalhes</a>
            </div>
        @empty
            <p>Nenhum veículo encontrado.</p>
        @endforelse

    </div>

@endsection
