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

    <a href="{{ url('/teste-admin') }}" class="botao-cadastrar">
        Voltar ao Painel
    </a>

    <a href="{{ url('/teste-admin-cadastrar-usuario') }}" class="botao-cadastrar">
        Cadastrar Novo Usuário
    </a>

    <table class="tabela-locacoes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Nível de Acesso</th>
                <th>Ações</th>
            </tr>
        </thead>
            <tbody>
                @forelse($usuarios ?? [] as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->role->value }}</td>

                        <td>
                            <a href="{{ url('/teste-admin-editar-usuario/' . $usuario->id) }}"
                                class="botao-cadastrar">
                                    Editar
                            </a>
                            
                            @if($usuario->id !== auth()->id())
                                <form action="{{ url('/teste-admin-usuarios/' . $usuario->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="botao-excluir">
                                        Excluir
                                    </button>
                                </form>
                            @else
                                <span>Usuário atual</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Nenhum usuário encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
    </table>
@endsection
