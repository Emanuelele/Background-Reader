<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/x-icon" href="https://www.peakville.it/favicon.ico">
        <!-- main style -->
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
        <!-- notify -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <!-- icons and fonts -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        
    </head>
    <body>
        <script>
            const backgroundDeleteUrl = "{{ route('background.delete') }}";
            const backgroundUpdateUrl = "{{ route('background.update') }}";
            const backgroundInfoUrl = "{{ route('background.info') }}";
            const backgroundResultUrl = "{{ route('background.result') }}";
            const backgroundReadUrl = "{{ route('background.read') }}";
            const backgroundDownloadUrl = "{{ route('background.save') }}";
            const backgroundDashboardStatsUrl = "{{ route('dashboard.stats') }}";
            const dashboardUrl = "{{ route('dashboard') }}";
            const csrfToken = "{{ csrf_token() }}";
        </script>
        <div class="container-scroller">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
                    <a class="sidebar-brand brand-logo" href="{{ route('dashboard') }}"><img src="https://www.peakville.it/_nuxt/logo.20020244.png" alt="logo" /></a>
                    <a class="sidebar-brand brand-logo-mini" href="{{ route('dashboard') }}"><img src="https://cdn.discordapp.com/icons/1015976925367378040/a_8aab7490e9efb8cc53487de73e4521c7.webp?size=96" alt="logo" /></a>
                </div>
                <ul class="nav">
                    <li class="nav-item profile">
                        <div class="profile-desc">
                            <div class="profile-pic">
                                <div class="count-indicator">
                                <img class="img-xs rounded-circle " src="{{ Auth::user()->avatar }}" alt="">
                                <span class="count bg-success"></span>
                            </div>
                            <div class="profile-name">
                                <h5 class="mb-0 font-weight-normal">{{ Auth::user()->username }}</h5>
                                <span>{{ Auth::user()->grade }}</span>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item nav-category">
                        <span class="nav-link">Navigation</span>
                    </li>
                    <li class="nav-item menu-items @if  (request()->is('dashboard')) active @endif">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <span class="menu-icon">
                                <i class="mdi mdi-speedometer"></i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <div style="margin-top: 5px;"></div>
                    <li class="nav-item menu-items @if (request()->is('background/*')) active @endif">
                        <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                            <span class="menu-icon">
                                <i class="mdi mdi-account-card-details"></i>
                            </span>
                            <span class="menu-title">Gestione BG</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="ui-basic">
                          <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ route('background.read') }}">Leggi un background</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('background.getall') }}">Tutti i background</a></li>
                          </ul>
                        </div>
                    </li>
                    <div style="margin-top: 5px;"></div>
                    <li class="nav-item menu-items">
                        <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                          <span class="menu-icon">
                            <i class="mdi mdi-check-decagram"></i>
                          </span>
                          <span class="menu-title">Gestione WL</span>
                          <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="auth">
                          <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="">Attesa whitelist</a></li>
                            <li class="nav-item"> <a class="nav-link" href="">Whitelist negate</a></li>
                            <li class="nav-item"> <a class="nav-link" href="">Utenti whitelistati</a></li>
                            <li class="nav-item"> <a class="nav-link" href="">Lista ban</a></li>
                          </ul>
                        </div>
                    </li>
                    <li class="nav-item menu-items">
                        <a class="nav-link" data-toggle="collapse" href="#log" aria-expanded="false" aria-controls="log">
                          <span class="menu-icon">
                            <i class="mdi mdi-security"></i>
                          </span>
                          <span class="menu-title">Log System</span>
                          <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="log">
                          <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.index', ['category' => 'PLAYER_JOIN']) }}">Log Ingressi</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.index', ['category' => 'PLAYER_QUIT']) }}">Log Uscite</a></li>
                          </ul>
                        </div>
                    </li>
                    <li class="nav-item menu-items">
                        <a class="nav-link" data-toggle="collapse" href="#logdiscord" aria-expanded="false" aria-controls="logdiscord">
                          <span class="menu-icon">
                            <i class="mdi mdi-book"></i>
                          </span>
                          <span class="menu-title">Log Discord</span>
                          <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="logdiscord">
                          <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'PLAYER_JOIN_DISCORD']) }}">Log Ingressi</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'PLAYER_QUIT_DISCORD']) }}">Log Uscite</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'PLAYER_DELETE_MESSAGE']) }}">Log mex. eliminati</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'ROLE_ADD_DISCORD']) }}">Ruoli aggiunti</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'ROLE_REMOVE_DISCORD']) }}">Ruoli rimossi</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'COMMAND_EXEC_DISCORD']) }}">Comandi eseguiti</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'VOICE_CHANNEL_JOIN_DISCORD']) }}">Join canale</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'VOICE_CHANNEL_LEFT_DISCORD']) }}">Left canale</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'BUTTON_CLICK_DISCORD']) }}">Pulsanti cliccati</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'MODAL_SUBMIT_DISCORD']) }}">Form inviati</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('logs.discord.index', ['category' => 'SELECT_MENU_INTERACTION_DISCORD']) }}">Select elements</a></li>
                          </ul>
                        </div>
                    </li>
                    <li class="nav-item menu-items">
                        <a class="nav-link" data-toggle="collapse" href="#db" aria-expanded="false" aria-controls="db">
                          <span class="menu-icon">
                            <i class="mdi mdi-database"></i>
                          </span>
                          <span class="menu-title">Gestione database</span>
                          <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="db">
                          <ul class="nav flex-column sub-menu">
                          <li class="nav-item"> <a class="nav-link" href="">Skills</a></li>
                          <li class="nav-item"> <a class="nav-link" href="">Money</a></li>
                          <li class="nav-item"> <a class="nav-link" href="">Peds</a></li>
                          </ul>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="container-fluid page-body-wrapper">
                <nav class="navbar p-0 fixed-top d-flex flex-row">
                    <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{ Auth::user()->avatar }}" alt="logo" /></a>
                    </div>
                    <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                        <span class="mdi mdi-menu"></span>
                    </button>
                    <ul class="navbar-nav w-100">
                        <li class="nav-item w-100">
                            <form class="nav-link mt-2 mt-md-0 d-none d-lg-flex search">
                                <input id ="searchbar" type="text" class="form-control" placeholder="Search"  style="width: 30%">
                            </form>
                        </li>
                    </ul>
                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                                <div class="navbar-profile">
                                    <img class="img-xs rounded-circle" src="{{ Auth::user()->avatar }}" alt="">
                                    <p class="mb-0 d-none d-sm-block navbar-profile-name">{{ Auth::user()->username }}</p>
                                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item preview-item">
                                    <div class="preview-thumbnail">
                                        <div class="preview-icon bg-dark rounded-circle">
                                            <i class="mdi mdi-logout text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="preview-item-content">
                                        <p onclick=" window.location.href = '{{ route('logout') }}'" class="preview-subject mb-1">Log Out</p>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                </a>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="mdi mdi-format-line-spacing"></span>
                    </button>
                </div>
                </nav>
                <div class="main-panel">
                    <div class="content-wrapper">
                        {{ $slot }}
                    </div>
                    <footer class="footer">
                        <div class="d-sm-flex justify-content-center justify-content-sm-between">
                            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Peakville 
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                            </span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
        <!-- navbar -->
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
        <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
        <script src="{{ asset('assets/js/misc.js') }}"></script>
        <!-- chart -->
        <script src="{{ asset('assets/js/chart.js') }}"></script>
        <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
        <!-- icons and fonts -->
        <script src="https://kit.fontawesome.com/eadf88c17f.js" crossorigin="anonymous"></script>
        <!-- notify -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <!-- main js -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <!-- others -->
        <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <script> showToast('{{ $error }}', true); </script>
            @endforeach
        @endif
      </body>
</html>