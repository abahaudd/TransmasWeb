{{-- Shared shell for all public CMS pages and account pages: head, sticky header, footer. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', config('app.name'))</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased cms-page" x-data="{ mobileMenuOpen: false, email: '' }">

    @include('cms.partials.header')

    @yield('content')

    @include('cms.partials.footer')

</body>
</html>
