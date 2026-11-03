@extends('layout.principal')

@section('titulo', 'Gerenciar Veículos - Locar')

@section('conteudo')

    <h2>Gerenciar Veículos</h2>

    <p>Confira os veículos cadastrados no sistema.</p>

    @if(session('sucesso'))
        <p style="color: green;">
            {{ session('sucesso') }}
        </p>
    @endif

    @foreach($errors->all() as $erro)
        <p style="color: red;">{{ $erro }}</p>
    @endforeach

    <div class="menu-gestor">
        <a href="{{ route('gestor.veiculos') }}">Gerenciar Veículos</a>
        <a href="{{ route('gestor.locacoes') }}">Gerenciar Locações</a>
    </div>

    <div class="gerenciar-veiculos">

        <a href="{{ route('gestor.veiculos.cadastrar') }}" class="botao-cadastrar">
            Cadastrar Veículo
        </a>

        <table class="tabela-locacoes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Ano</th>
                    <th>Diária</th>
                    <th>Disponibilidade</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($veiculos as $veiculo)
                    <tr>
                        <td>{{ $veiculo->id }}</td>
                        <td>{{ $veiculo->brand }}</td>
                        <td>{{ $veiculo->model }}</td>
                        <td>{{ $veiculo->model_year }}</td>
                        <td>R$ {{ number_format($veiculo->daily_rate, 2, ',', '.') }}</td>
                        <td>
                            @if($veiculo->available)
                                Disponível
                            @else
                                Indisponível
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('gestor.veiculos.editar', $veiculo->id) }}"
                                class="botao-cadastrar">
                                Editar
                            </a>

                            <form action="{{ route('gestor.veiculos.disponibilidade', $veiculo->id) }}"
                                  method="POST"
                                  style="display: inline;"
                                  onsubmit="return confirm('Deseja alterar a disponibilidade deste veículo?')">

                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="available" value="{{ $veiculo->available ? 0 : 1 }}">

                                <button type="submit"
                                        class="{{ $veiculo->available ? 'botao-excluir' : 'botao-cadastrar' }}">
                                    {{ $veiculo->available ? 'Desabilitar' : 'Habilitar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum veículo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

@endsection