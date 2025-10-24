<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/js/app.ts')
        @vite('resources/css/app.css')
        @inertiaHead
    </head>
    <body>
        <div class="isolate">
            @inertia
        </div>
    </body>
</html>