<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cinefy</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite('resources/css/app.css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/framer-motion/10.12.0/framer-motion.min.js" defer></script>
    <style>
        /* Background Animation */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            z-index: -1;
        }

        .icon {
            position: absolute;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.2);
            transition: transform 0.1s ease-out;
        }
    </style>
</head>

<body
    class="flex items-center justify-center min-h-screen bg-gradient-to-r from-black via-gray-900 to-black text-white relative overflow-hidden">

    <!-- Lingkaran Blur dengan Glow -->
    <div class="circle green"></div>
    <div class="circle pink"></div>

    <!-- Background Animation -->
    <div class="background-animation" id="background-animation"></div>

    <!-- Login Container -->
    <div id="login-container"
        class="w-3/4 max-w-4xl bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex relative z-10">

        <!-- Left Section (hidden on mobile) -->
        <div class="hidden w-1/2 px-10 m-auto flex flex-col  justify-center items-center md:block">
            <img src="{{ asset('images/logocinefy.png') }}" alt="MyWatchLog Logo" class="w-auto h-20 md:h-24">
            <p class="text-gray-400 text-center mt-4">Explore, track, and manage your favorite movies & shows with Cinefy!</p>
            <!-- Ganti dengan path ke logo kamu -->
        </div>


        <!-- Right Section (responsive for mobile) -->
        <div class="w-full md:w-1/2 p-10 bg-gray-900 rounded-2xl">
            <h2 class="text-2xl font-bold text-center text-blue-400">Login</h2>
            <form class="mt-6" method="POST" action="{{ route('login.post') }}">
                @csrf
                <div>
                    <label class="block text-gray-300">User Name</label>
                    <input type="text" name="username"
                        class="w-full px-4 py-2 mt-2 bg-gray-700 border border-gray-600 rounded-md focus:ring-2 focus:ring-blue-400 text-white"
                        placeholder="Enter your username" value="{{ old('username') }}">
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mt-4" x-data="{ show: false }">
                    <label class="block text-gray-300">Password</label>
                    <div class="relative mt-2">
                        <input :type="show ? 'text' : 'password'" name="password"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:ring-2 focus:ring-blue-400 text-white"
                            placeholder="Enter your password">
                        <button type="button"
                            class="absolute inset-y-0 right-0 px-3 py-2 text-sm font-medium text-blue-400"
                            @click="show = !show">SHOW</button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex justify-between items-center mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox bg-gray-700 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-400 hover:underline">Forgot Password?</a>
                </div>
                <button type="submit" class="w-full mt-4 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Login</button>
            </form>
            
            <p class="mt-4 text-sm text-center text-gray-300">
                Don’t have an account? <a href="{{ route('register') }}" class="text-blue-400 hover:underline">Sign
                    Up</a>
            </p>

            <!-- Tombol Login dengan Google -->
            <a href="{{ url('login/google') }}"
                class="w-full mt-4 bg-red-600 text-white py-2 rounded-md flex items-center justify-center gap-3 hover:bg-red-700">
                <ion-icon name="logo-google" class="text-xl"></ion-icon>
                Login with Google
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // Ikon untuk tema Film
        const icons = ["🎬", "🎥", "📽️", "🍿", "🎞️"];
        const numIcons = 20; // Jumlah ikon yang muncul
        const backgroundAnimation = document.getElementById("background-animation");

        // Generate random posisi untuk ikon
        for (let i = 0; i < numIcons; i++) {
            let span = document.createElement("span");
            span.innerHTML = icons[Math.floor(Math.random() * icons.length)];
            span.classList.add("icon");
            span.style.top = Math.random() * 100 + "vh";
            span.style.left = Math.random() * 100 + "vw";
            span.style.transform = `scale(${Math.random() * 1.5 + 0.5})`;
            backgroundAnimation.appendChild(span);
        }

        // Efek pergerakan ikon mengikuti mouse
        document.addEventListener("mousemove", (event) => {
            document.querySelectorAll(".icon").forEach((icon) => {
                let moveX = (event.clientX / window.innerWidth - 0.5) * 30;
                let moveY = (event.clientY / window.innerHeight - 0.5) * 30;
                icon.style.transform =
                    `translate(${moveX}px, ${moveY}px) scale(${Math.random() * 1.5 + 0.5})`;
            });
        });

        // Animasi muncul login box
        document.addEventListener("DOMContentLoaded", function() {
            const loginContainer = document.getElementById("login-container");
            loginContainer.style.opacity = 0;
            loginContainer.style.transform = "translateY(50px)";
            setTimeout(() => {
                loginContainer.style.transition = "all 0.6s ease-out";
                loginContainer.style.opacity = 1;
                loginContainer.style.transform = "translateY(0)";
            }, 100);
        });
    </script>

</body>

</html>
