<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- FORCE LIGHT THEME --}}
        <style>
            /* Override semua dark theme */
            html, html.dark, body {
                background-color: #ffffff !important;
                color: #1f2937 !important;
            }
            
            /* Force light colors untuk semua elemen */
            .bg-background, [class*="bg-background"] {
                background-color: #ffffff !important;
            }
            
            .text-foreground, [class*="text-foreground"] {
                color: #1f2937 !important;
            }
            
            .border-border, [class*="border-border"] {
                border-color: #e5e7eb !important;
            }
            
            .bg-muted, [class*="bg-muted"] {
                background-color: #f9fafb !important;
            }
            
            .text-muted-foreground, [class*="text-muted-foreground"] {
                color: #6b7280 !important;
            }
            
            /* Sidebar tetap putih */
            aside {
                background-color: #ffffff !important;
                border-color: #e5e7eb !important;
            }
            
            /* Cards putih */
            .rounded-lg {
                background-color: #ffffff !important;
                border-color: #e5e7eb !important;
            }
        </style>

        <script>
            // Remove any dark classes that might be added
            document.addEventListener('DOMContentLoaded', function() {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark');
                // Force light mode every 100ms untuk memastikan
                setInterval(() => {
                    document.documentElement.classList.remove('dark');
                    document.body.classList.remove('dark');
                }, 100);
            });
        </script>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        @routes
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
