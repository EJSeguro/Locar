@extends('layout.principal')

@section('titulo', 'Detalhes do Veículo - Locar')

@section('conteudo')

    <h2>Detalhes do Veículo</h2>

    <div class="detalhes-veiculo">

        <h3>{{ $veiculo->brand }} {{ $veiculo->model }}</h3>

        <p>Ano: {{ $veiculo->model_year }}</p>

        <p>Combustível: {{ $veiculo->fuel }}</p>

        <p>Diária: R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</p>

        @auth
            @if(auth()->user()->role === \App\Enums\Role::Cliente && $veiculo->available)

                <h3>Alugar Veículo</h3>

                <form action="{{ route('cliente.locacoes.salvar') }}" method="POST">

                    @csrf

                    <input type="hidden" name="vehicle_id" value="{{ $veiculo->id }}">

                    <label for="start_date">Data de retirada:</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('start_date') }}"
                        required
                    >

                    <label for="end_date">Data de devolução:</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        value="{{ old('end_date') }}"
                        required
                    >

                    <button type="submit">Alugar</button>

                </form>

                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                @endif

            @endif
        @endauth

    </div>

    <p>
        <a href="{{ route('veiculos') }}">
            Voltar para Veículos
        </a>
    </p>

@endsection