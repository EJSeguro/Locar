
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
                <a href="{{url('/teste-blade') }}">Início</a>
                <a href="{{ route('veiculos') }}">Veículos</a>    
            @guest
                <a href="{{ url('/teste-login') }}">Login</a>
                <a href="{{ url('/teste-cadastro') }}">Cadastro</a>
            @endguest

            @auth
                @if(auth()->user()->role === app\enums\Role::Cliente)
                    <a href="{{ url('/teste-locacoes') }}">Minhas Locações</a>
                @endif

                @if(auth()->user()->role === app\enums\Role::Gestor)
                    <a href="{{ url('/teste-gestor-veiculos') }}">Gerenciar Veículos</a>
                    <a href="{{ url('/teste-gestor-locacoes') }}">Gerenciar Locações</a>
                @endif

                @if(auth()->user()->role === app\enums\Role::Admin)
                    <a href="{{ url('/teste-admin') }}">Painel Administrativo</a>
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
