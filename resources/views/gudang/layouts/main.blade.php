<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stagging Gudang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    @vite('resources/css/app.css')
</head>
<body>

@include('gudang.layouts.header')
@include('gudang.layouts.sidebar')


<div class="p-4 h-screen sm:ml-64 mt-14 dark:bg-gray-800">
    @yield('container')
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
<script src="/js/toggle.js"></script>
</body>
</html>