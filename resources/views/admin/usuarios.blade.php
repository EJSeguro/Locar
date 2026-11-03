@extends('layout.principal')

@section('titulo', 'Gerenciar Usuários - Locar')

@section('conteudo')

    <h2>Gerenciar Usuários</h2>

    <p>Confira os usuários cadastrados no sistema.</p>

    @if(session('sucesso'))
        <p style="color: green;">
            {{ session('sucesso') }}
        </p>
    @endif

    @error('user')
        <p style="color: red;">
            {{ $message }}
        </p>
    @enderror

    <a href="{{ route('admin.painel') }}" class="botao-cadastrar">
        Voltar ao Painel
    </a>

    <a href="{{ route('admin.usuarios.cadastrar') }}" class="botao-cadastrar">
        Cadastrar Novo Usuário
    </a>

    <table class="tabela-locacoes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Nível de Acesso</th>
                <th>Locações</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->role->value }}</td>
                    <td>{{ $usuario->rentals_count }}</td>

                    <td>
                        <a href="{{ route('admin.usuarios.editar', $usuario->id) }}"
                           class="botao-cadastrar">
                            Editar
                        </a>

                        @if($usuario->id !== auth()->id() && $usuario->rentals_count == 0)
                            <form action="{{ route('admin.usuarios.excluir', $usuario->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="botao-excluir">
                                    Excluir
                                </button>
                            </form>
                        @elseif($usuario->id === auth()->id())
                            <span>Usuário atual</span>
                        @else
                            <span>Não pode excluir (possui locações)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection