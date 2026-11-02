@extends('layout.principal')

@section('titulo', 'Gerenciar Locações - Locar')

@section('conteudo')

    <h2>Gerenciar Locações</h2>

    <p>Confira as locações realizadas pelos clientes.</p>

    <div class="menu-gestor">
        <a href="{{ route('gestor.veiculos') }}">Gerenciar Veículos</a>
        <a href="{{ route('gestor.locacoes') }}">Gerenciar Locações</a>
    </div>

    <table class="tabela-locacoes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Veículo</th>
                <th>Retirada</th>
                <th>Devolução</th>
                <th>Valor</th>
                <th>Situação</th>
            </tr>
        </thead>

        <tbody>
            @forelse($locacoes ?? [] as $locacao)
                <tr>
                    <td>{{ $locacao->id }}</td>
                    <td>{{ $locacao->cliente }}</td>
                    <td>{{ $locacao->veiculo }}</td>
                    <td>{{ $locacao->retirada }}</td>
                    <td>{{ $locacao->devolucao }}</td>
                    <td>R$ {{ number_format($locacao->valor, 2, ',', '.') }}</td>
                    <td>{{ $locacao->situacao }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhuma locação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection