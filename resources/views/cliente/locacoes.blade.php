@extends('layout.principal')

@section('titulo', 'Minhas Locações - Locar')

@section('conteudo')

    <h2>Minhas Locações</h2>

    <p>Acompanhe aqui suas locações de veículos.</p>

    @if(session('sucesso'))
        <p>{{ session('sucesso') }}</p>
    @endif

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
            @forelse($locacoes as $locacao)
                <tr>
                    <td>
                        {{ $locacao->vehicle->brand }}
                        {{ $locacao->vehicle->model }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($locacao->start_date)->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($locacao->end_date)->format('d/m/Y') }}
                    </td>

                    <td>
                        R$ {{ number_format($locacao->total, 2, ',', '.') }}
                    </td>

                    <td>
                        {{ ucfirst($locacao->status->value) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        Você ainda não possui nenhuma locação.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection