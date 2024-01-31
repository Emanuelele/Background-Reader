<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            body, html {
                margin: 0;
                padding: 0;
                height: 100%;
            }
    
            iframe {
                width: 100%;
                height: 100vh; 
                border: none;
            }
        </style>
    </head>
    <body>
        @php
            $link = asset('backgrounds/');
            $link .= "/";
            $link .= $filename;
        @endphp
        <iframe src="{{ $link }}" frameborder="0"></iframe>
    </body>
</html>