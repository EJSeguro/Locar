@extends('layout.principal')

@section('titulo', 'Login - Locar')

@section('conteudo')

    <h2>Entrar no Sistema</h2>

    <p>Informe seus dados para acessar sua conta.</p>

    <div class="form-login">

        @if(session('sucesso'))
            <p style="color: green;">
                {{ session('sucesso') }}
            </p>
        @endif

        @if(session('status'))
            <p style="color: green;">
                {{ session('status') }}
            </p>
        @endif

        @if($errors->any())
            <p style="color: red;">
                E-mail ou senha inválidos.
            </p>
        @endif

        <form action="{{ route('login.store') }}" method="POST">
            @csrf

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="password"
                   placeholder="Digite sua senha" required>

            <button type="submit">Entrar</button>
        </form>

        <p>
            Ainda não possui uma conta?
            <a href="{{ route('cadastro') }}">Cadastre-se</a>
        </p>

    </div>

@endsection