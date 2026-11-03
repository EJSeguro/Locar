@extends('layout.principal')

@section('titulo', 'Editar Veículo - Locar')

@section('conteudo')

    <h2>Editar Veículo</h2>

    <p>Altere a placa ou a diária. Os dados da FIPE não podem ser alterados.</p>

    <div class="form-cadastro">

        @if($errors->any())
            <p style="color: red;">Verifique os dados informados.</p>

            @foreach($errors->all() as $erro)
                <p style="color: red;">{{ $erro }}</p>
            @endforeach
        @endif

        <form action="{{ route('gestor.veiculos.atualizar', $veiculo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="plate">Placa:</label>
            <input type="text" id="plate" name="plate"
                   value="{{ old('plate', $veiculo->plate) }}" required>

            <label for="daily_rate">Valor da diária:</label>
            <input type="number" id="daily_rate" name="daily_rate"
                   step="0.01" min="0.01"
                   value="{{ old('daily_rate', $veiculo->daily_rate) }}" required>

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

            <button type="submit">Salvar Alterações</button>
        </form>

        <form action="{{ route('gestor.veiculos.excluir', $veiculo->id) }}"
              method="POST"
              onsubmit="return confirm('Deseja excluir este veículo?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="botao-excluir">Excluir Veículo</button>
        </form>

        <p>
            <a href="{{ route('gestor.veiculos') }}" class="botao-cadastrar">
                Voltar para Veículos
            </a>
        </p>

    </div>

@endsection
