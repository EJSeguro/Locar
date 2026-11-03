@extends('layout.principal')

@section('titulo', 'Minhas Locações - Locar')

@section('conteudo')

    <h2>Minhas Locações</h2>

    <p>Acompanhe aqui suas locações de veículos.</p>

    @if(session('sucesso'))
        <p style="color: green;">{{ session('sucesso') }}</p>
    @endif

    @if($errors->has('status'))
        <p style="color: red;">{{ $errors->first('status') }}</p>
    @endif

    <table class="tabela-locacoes">
        <thead>
            <tr>
                <th>Veículo</th>
                <th>Retirada</th>
                <th>Devolução</th>
                <th>Valor</th>
                <th>Situação</th>
                <th>Ações</th>
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

                    <td>
                        @if(
                            $locacao->status === \App\Enums\RentalStatus::Ativa &&
                            $locacao->start_date->gt(today())
                        )
                            <form action="{{ route('cliente.locacoes.cancelar', $locacao->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Deseja realmente cancelar esta locação?')">

                                @csrf
                                @method('PATCH')

                                <button type="submit" class="botao-excluir">
                                    Cancelar
                                </button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        Você ainda não possui nenhuma locação.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection