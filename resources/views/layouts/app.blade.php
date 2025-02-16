<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cinefy - Explore Cinema</title>

    <link id="favicon" rel="icon"
        href="https://res.cloudinary.com/dj2pofe14/image/upload/v1739474306/favicon_xo99il.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    @vite('resources/css/app.css')

    <style>
        /* Loader Container */
        #loader {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* Custom Loader */
        .loader {
            width: 6em;
            height: 6em;
            font-size: 10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader .face {
            position: absolute;
            border-radius: 50%;
            border-style: solid;
            animation: animate023845 3s linear infinite;
        }

        .loader .face:nth-child(1) {
            width: 100%;
            height: 100%;
            color: gold;
            border-color: currentColor transparent transparent currentColor;
            border-width: 0.2em 0.2em 0em 0em;
            --deg: -45deg;
            animation-direction: normal;
        }

        .loader .face:nth-child(2) {
            width: 70%;
            height: 70%;
            color: lime;
            border-color: currentColor currentColor transparent transparent;
            border-width: 0.2em 0em 0em 0.2em;
            --deg: -135deg;
            animation-direction: reverse;
        }

        .loader .face .circle {
            position: absolute;
            width: 50%;
            height: 0.1em;
            top: 50%;
            left: 50%;
            background-color: transparent;
            transform: rotate(var(--deg));
            transform-origin: left;
        }

        .loader .face .circle::before {
            position: absolute;
            top: -0.5em;
            right: -0.5em;
            content: '';
            width: 1em;
            height: 1em;
            background-color: currentColor;
            border-radius: 50%;
            box-shadow: 0 0 2em, 0 0 4em, 0 0 6em, 0 0 8em, 0 0 10em, 0 0 0 0.5em rgba(255, 255, 0, 0.1);
        }

        @keyframes animate023845 {
            to {
                transform: rotate(1turn);
            }
        }
    </style>

</head>

<body class="overflow-y-auto w-full relative overflow-hidden bg-gradient-to-r from-black via-gray-900 to-black"
    x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1500)">

    <!-- Loader -->
    <div x-show="loading" x-transition.opacity.duration.500ms id="loader">
        <div class="loader">
            <div class="face">
                <div class="circle"></div>
            </div>
            <div class="face">
                <div class="circle"></div>
            </div>
        </div>
    </div>

    <div>
        <!-- Navbar -->
        @include('components.navbar')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @include('components.footer')
    </div>

</body>

</html>
