@extends('layout.principal')

@section('titulo', 'Minhas Locações - Locar')

@section('conteudo')

    <h2>Minhas Locações</h2>

    <p>Acompanhe aqui suas locações de veículos.</p>

    <table class="tabela-locacoes">
        <thead>
            <tr>
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
                    <td>{{ $locacao->veiculo }}</td>
                    <td>{{ $locacao->retirada }}</td>
                    <td>{{ $locacao->devolucao }}</td>
                    <td>R$ {{ number_format($locacao->valor, 2, ',', '.') }}</td>
                    <td>{{ $locacao->situacao }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        Nenhuma locação disponível para exibição.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection