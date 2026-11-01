@extends('layout.principal')

@section('titulo', 'Gerenciar Veículos - Locar')

@section('conteudo')

    <h2>Gerenciar Veículos</h2>

    <p>Confira os veículos cadastrados no sistema.</p>

    <div class="menu-gestor">
        <a href="{{ url('/teste-gestor-veiculos') }}">Gerenciar Veículos</a>
        <a href="{{ url('/teste-gestor-locacoes') }}">Gerenciar Locações</a>
    </div>

    <div class="gerenciar-veiculos">

        <a href="{{ url('/teste-gestor-cadastrar') }}" class="botao-cadastrar">Cadastrar Veículo</a>

        <table class="tabela-locacoes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Ano</th>
                    <th>Diária</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($veiculos ?? [] as $veiculo)
                    <tr>
                        <td>{{ $veiculo->id }}</td>
                        <td>{{ $veiculo->brand }}</td>
                        <td>{{ $veiculo->model }}</td>
                        <td>{{ $veiculo->model_year }}</td>
                        <td>R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ url('/teste-gestor-editar') }}" class="botao-cadastrar">Editar</a>
                            
                            <button type="button" onclick="confirm('Deseja realmente excluir este veículo?')">Excluir</button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Nenhum veículo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection
