@extends('layout.principal')

@section('titulo', 'Cadastrar Usuário - Locar')

@section('conteudo')

    <h2>Cadastrar Usuário</h2>

    <p>Preencha os dados para cadastrar um novo usuário no sistema.</p>

    <div class="form-login">

        @if($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.usuarios.salvar') }}" method="POST">
            @csrf

            <label for="name">Nome:</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirmar Senha:</label>
            <input type="password" id="password_confirmation"
                   name="password_confirmation" required>

            <label for="role">Nível de Acesso:</label>
            <select id="role" name="role" required>
                <option value="">Selecione um perfil</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                    Administrador
                </option>
                <option value="gestor" {{ old('role') == 'gestor' ? 'selected' : '' }}>
                    Gestor
                </option>
            </select>

            <button type="submit">Cadastrar Usuário</button>
        </form>

        <p>
            <a href="{{ route('admin.usuarios') }}">Voltar para Usuários</a>
        </p>

    </div>

@endsection