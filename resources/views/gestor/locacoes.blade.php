@extends('layout.principal')

@section('titulo', 'Gerenciar Locações - Locar')

@section('conteudo')

    <h2>Gerenciar Locações</h2>

    <p>Confira as locações realizadas pelos clientes.</p>

    @if (session('sucesso'))
        <p style="color: green;">{{ session('sucesso') }}</p>
    @endif

    @if ($errors->has('status'))
        <p style="color: red;">{{ $errors->first('status') }}</p>
    @endif

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
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($locacoes as $locacao)
                <tr>
                    <td>{{ $locacao->id }}</td>

                    <td>{{ $locacao->user->name }}</td>

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

                    <td>{{ ucfirst($locacao->status->value) }}</td>

                    <td>
                        @if($locacao->status === \App\Enums\RentalStatus::Ativa)
                            <form action="{{ route('gestor.locacoes.finalizar', $locacao->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja finalizar esta locação?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="botao-cadastrar">
                                    Finalizar
                                </button>
                            </form>
                        @else
                            -
                    @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Nenhuma locação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection