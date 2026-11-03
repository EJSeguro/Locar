<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Locar')</title>
    <link rel="stylesheet" href="{{ asset('css/estilo.css') }}">
</head>
<body>
    <header>
        <h1>LOCAR</h1>
        <p>Sistema de Locação de Veículos</p>

        <nav>
            <a href="{{ route('home') }}">Início</a>
            <a href="{{ route('veiculos') }}">Veículos</a>

            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('cadastro') }}">Cadastro</a>
            @endguest

            @auth
                @if(auth()->user()->role === \App\Enums\Role::Cliente)
                    <a href="{{ route('cliente.locacoes') }}">Minhas Locações</a>
                @endif

                @if(auth()->user()->role === \App\Enums\Role::Gestor)
                    <a href="{{ route('gestor.veiculos') }}">Gerenciar Veículos</a>
                    <a href="{{ route('gestor.locacoes') }}">Gerenciar Locações</a>
                @endif

                @if(auth()->user()->role === \App\Enums\Role::Admin)
                    <a href="{{ route('admin.painel') }}">Painel Administrativo</a>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Sair</button>
                </form>
            @endauth
        </nav>
    </header>

    <main>
        @yield('conteudo')
    </main>

    <footer>
        <p>Locar - Sistema de Locação de Veículos</p>
    </footer>
</body>
</html>
