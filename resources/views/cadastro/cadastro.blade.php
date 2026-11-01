@extends('layout.principal')

@section('titulo', 'Cadastro - Locar')

@section('conteudo')

    <h2>Cadastro de cliente</h2>

    <p>Preencha os dados abaixo para criar sua conta.</p>

    <div class="form-cadastro">
        <form>
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <label for="confirmar_senha">Confirmar senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>

            <button type="button">Cadastrar</button>
        </form>

        <p>Já possui uma conta? <a href="{{ url('/teste-login') }}">Faça login</a></p>
    </div>

@endsection
