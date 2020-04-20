<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">

</head>
<body class="bg-gray-200">
<div class="container mx-auto">
    <div class="max-w-screen-xl mx-auto py-8 px-8 flex items-center justify-between">
        <h2 class="text-3xl leading-9 font-extrabold tracking-tight text-gray-900">
            Item list
            <br/>
            <span data-total-count-items class="text-indigo-600">Showing 0 items</span>
        </h2>
        <div class="flex flex-shrink-0 mt-0">
            <div class="inline-flex rounded-md shadow">
                <button data-add-modal-trigger type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700 transition duration-150 ease-in-out">
                    <svg fill="none" stroke="currentColor" stroke-linecap="round"
                         stroke-linejoin="round"
                         stroke-width="3" viewBox="0 0 24 24" class="-ml-1 mr-2 h-5 w-5">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    New item
                </button>
            </div>
        </div>
    </div>

    <div class="item-list-grid" id="simpleList"></div>
</div>
</body>
</html>
