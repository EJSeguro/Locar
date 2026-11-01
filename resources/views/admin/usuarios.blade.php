@extends('layout.principal')

@section('titulo', 'Gerenciar Usuários - Locar')

@section('conteudo')

    <h2>Gerenciar Usuários</h2>

    <p>Confira os usuários cadastrados no sistema.</p>

    <a href="{{ url('/teste-admin') }}" class="botao-cadastrar">
        Voltar ao Painel
    </a>

    <table class="tabela-locacoes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Nível de Acesso</th>
            </tr>
        </thead>

        <tbody>
            @forelse($usuarios ?? [] as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->role }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum usuário encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection
