@extends('layout.principal')

@section('titulo', 'Editar Veículo - Locar')

@section('conteudo')

    <h2>Editar Veículo</h2>

    <p>Altere as informações do veículo abaixo.</p>

    <div class="form-cadastro">
        <form>
            <label for="plate">Placa:</label>
            <input type="text" id="plate" name="plate"
                value="{{ $veiculo->plate ?? '' }}" required>

            <label for="brand">Marca:</label>
            <input type="text" id="brand" name="brand"
                value="{{ $veiculo->brand ?? '' }}" required>

            <label for="model">Modelo:</label>
            <input type="text" id="model" name="model"
                value="{{ $veiculo->model ?? '' }}" required>

            <label for="model_year">Ano:</label>
            <input type="number" id="model_year" name="model_year"
                value="{{ $veiculo->model_year ?? '' }}" required>

            <label for="fuel">Combustível:</label>
            <input type="text" id="fuel" name="fuel"
                value="{{ $veiculo->fuel ?? '' }}" required>

            <label for="fipe_code">Código FIPE:</label>
            <input type="text" id="fipe_code" name="fipe_code"
                value="{{ $veiculo->fipe_code ?? '' }}" required>

            <label for="year_id">Código do ano FIPE:</label>
            <input type="text" id="year_id" name="year_id"
                value="{{ $veiculo->year_id ?? '' }}" required>

            <label for="fipe_price">Valor FIPE:</label>
            <input type="number" id="fipe_price" name="fipe_price"
                step="0.01" min="0"
                value="{{ $veiculo->fipe_price ?? '' }}" required>

            <label for="daily_rate">Valor da diária:</label>
            <input type="number" id="daily_rate" name="daily_rate"
                step="0.01" min="0"
                value="{{ $veiculo->daily_rate ?? '' }}" required>

            <button type="button">Salvar Alterações</button>
        </form>
    </div>

@endsection
