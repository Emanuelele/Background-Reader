
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="{{ asset('assets/login/style.css') }}">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    </head>
    <body>
        @if ($errors->any())
            <script defer type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
            @foreach ($errors->all() as $error)
                <script>
                    Toastify({
                        text: "{{ $error }}",
                        close: true,
                        style: {
                            background: "linear-gradient(to right, rgb(255, 95, 109), rgb(189, 89, 17))"
                        }
                    }).showToast();
                </script>
            @endforeach
        @endif
        <div id="__nuxt">
            <div class="bg-home-background h-screen w-screen bg-[length:auto_120%] bg-center bg-no-repeat">
                <div class="flex h-screen w-screen flex-col items-center justify-center bg-p-black-500/80">
                    <img src="https://www.peakville.it/_nuxt/logo.20020244.png" alt="Peakville" class="block w-40 md:w-52" draggable="false">
                    <div class="mt-9 text-xl font-bold text-p-beige-100 md:text-2xl"> Gestionale Peakville '70 vibes </div>
                    <div class="mt-7 flex w-fit flex-col gap-4 md:flex-row">
                        <a href="{{ route('login.handler') }}" target="_self" class="block w-fit">
                            <button class="block w-fit rounded-xl border-2 border-p-beige-100 px-6 py-2 font-heading text-p-beige-500 outline outline-2 transition-colors md:px-8 md:py-3 bg-p-blue-500 outline-p-blue-900 hover:bg-p-blue-900">
                                Login with Discord
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>      
</html>



