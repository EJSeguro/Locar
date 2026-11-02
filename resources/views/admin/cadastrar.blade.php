
@extends('layout.principal')

@section('titulo', 'Cadastrar Usuário - Locar')

@section('conteudo')

    <h2>Cadastrar Usuário</h2>

    <p>Preencha os dados para cadastrar um novo usuário no sistema.</p>

    <div class="form-login">

        <form action="{{ url('/teste-admin-cadastrar-usuario') }}" method="POST">
            @csrf

            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirmar Senha:</label>
            <input type="password" id="password_confirmation"
                   name="password_confirmation" required>

            <label for="role">Nível de Acesso:</label>
            <select id="role" name="role" required>
                <option value="">Selecione um perfil</option>
                <option value="admin">Administrador</option>
                <option value="gestor">Gestor</option>
                <option value="cliente">Cliente</option>
            </select>

            <button type="submit">Cadastrar Usuário</button>

        </form>

        <p>
            <a href="{{ url('/teste-admin-usuarios') }}">Voltar para Usuários</a>
        </p>

    </div>

@endsection
