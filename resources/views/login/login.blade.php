@extends('layout.principal')

@section('titulo', 'Login - Locar')

@section('conteudo')

    <h2>Entrar no sistema</h2>

    <p>Informe seus dados para acessar sua conta.</p>

    <div class="form-login">
        <form>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <button type="button">Entrar</button>
        </form>

        <p>Ainda não possui uma conta? <a href="#">Cadastre-se</a></p>
    </div>

@endsection
