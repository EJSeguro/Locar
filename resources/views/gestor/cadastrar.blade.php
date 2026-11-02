@extends('layout.principal')

@section('titulo', 'Cadastrar Veículo - Locar')

@section('conteudo')

    <h2>Cadastrar Veículo</h2>

    <p>Informe a placa e os códigos FIPE para cadastrar um veículo.</p>

    <div class="form-cadastro">

        @if($errors->any())
            <p style="color: red;">Verifique os dados informados.</p>

            @foreach($errors->all() as $erro)
                <p style="color: red;">{{ $erro }}</p>
            @endforeach
        @endif

        <form action="{{ route('gestor.veiculos.salvar') }}" method="POST">
            @csrf

            <label for="plate">Placa:</label>
            <input type="text" id="plate" name="plate"
                   value="{{ old('plate') }}"
                   placeholder="ABC1D23" required>

            <label for="fipe_code">Código FIPE:</label>
            <input type="text" id="fipe_code" name="fipe_code"
                   value="{{ old('fipe_code') }}"
                   placeholder="004001-0" required>

            <label for="year_id">Código do ano FIPE:</label>
            <input type="text" id="year_id" name="year_id"
                   value="{{ old('year_id') }}"
                   placeholder="2024-1" required>

            <button type="submit">Cadastrar Veículo</button>
        </form>

        <p>
            <a href="{{ route('gestor.veiculos') }}">
                Voltar para Veículos
            </a>
        </p>

    </div>

@endsection