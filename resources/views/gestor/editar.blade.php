@extends('layout.principal')

@section('titulo', 'Detalhes do Veículo - Locar')

@section('conteudo')

    <h2>Informações do Veículo</h2>

    <p>Confira as informações do veículo cadastrado.</p>

    <div class="form-cadastro">

        <label for="plate">Placa:</label>
        <input type="text" id="plate"
               value="{{ $veiculo->plate }}" readonly>

        <label for="brand">Marca:</label>
        <input type="text" id="brand"
               value="{{ $veiculo->brand }}" readonly>

        <label for="model">Modelo:</label>
        <input type="text" id="model"
               value="{{ $veiculo->model }}" readonly>

        <label for="model_year">Ano:</label>
        <input type="number" id="model_year"
               value="{{ $veiculo->model_year }}" readonly>

        <label for="fuel">Combustível:</label>
        <input type="text" id="fuel"
               value="{{ $veiculo->fuel }}" readonly>

        <label for="fipe_code">Código FIPE:</label>
        <input type="text" id="fipe_code"
               value="{{ $veiculo->fipe_code }}" readonly>

        <label for="year_id">Código do ano FIPE:</label>
        <input type="text" id="year_id"
               value="{{ $veiculo->year_id }}" readonly>

        <label for="fipe_price">Valor FIPE:</label>
        <input type="number" id="fipe_price"
               step="0.01"
               value="{{ $veiculo->fipe_price }}" readonly>

        <label for="daily_rate">Valor da diária:</label>
        <input type="number" id="daily_rate"
               step="0.01"
               value="{{ $veiculo->daily_rate }}" readonly>

        <p>
            <a href="{{ route('gestor.veiculos') }}" class="botao-cadastrar">
                Voltar para Veículos
            </a>
        </p>

    </div>

@endsection