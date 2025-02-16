<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <title>Register - Cinefy</title>
    <link id="favicon" rel="icon"
    href="https://res.cloudinary.com/dj2pofe14/image/upload/v1739474306/favicon_xo99il.png" type="image/png">
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

    <!-- Background Animation -->
    <div class="background-animation" id="background-animation"></div>

    <!-- Register Container -->
    <div id="register-container"
        class="w-3/4 max-w-4xl bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex relative z-10">

        <!-- Left Section (hidden on mobile) -->
        <div class="hidden w-1/2 px-10 m-auto flex flex-col  justify-center items-center md:block">
            <img src="{{ asset('images/logocinefy.png') }}" alt="MyWatchLog Logo" class="w-auto h-20 md:h-24">
            <p class="text-gray-400 text-center mt-4">Explore, track, and manage your favorite movies & shows with Cinefy!</p>
            <!-- Ganti dengan path ke logo kamu -->
        </div>

        <!-- Right Section (responsive for mobile) -->
        <div class="w-full md:w-1/2 p-10 bg-gray-900 rounded-2xl">
            <h2 class="text-2xl font-bold text-center text-blue-400">Create an Account</h2>

            @if (session('success'))
                <p class="text-green-400 text-center">{{ session('success') }}</p>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-gray-300">Full Name</label>
                    <input type="text" name="name" required
                        class="mt-2 w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:ring-2 focus:ring-blue-400"
                        placeholder="Enter your full name">
                </div>

                <div>
                    <label class="block text-gray-300">Email</label>
                    <input type="email" name="email" id="email" required
                        class="mt-2 w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:ring-2 focus:ring-blue-400"
                        placeholder="Enter your email">
                    <p id="email-error" class="text-red-400 text-sm mt-1 hidden">This email is already registered.</p>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-gray-300">Password</label>
                    <div class="mt-2 relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:ring-2 focus:ring-blue-400"
                            placeholder="Enter your password">
                        <button type="button" class="absolute inset-y-0 right-3 text-blue-400 text-sm"
                            @click="show = !show">SHOW</button>
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-gray-300">Confirm Password</label>
                    <div class=" mt-2 relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" id="confirm-password"
                            required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:ring-2 focus:ring-blue-400"
                            placeholder="Confirm your password">
                        <p id="password-length-error" class="text-red-400 text-sm mt-1 hidden">Password must be at least 6 characters</p>
                        <button type="button" class="absolute inset-y-0 right-3 text-blue-400 text-sm"
                            @click="show = !show">SHOW</button>
                    </div>
                    <p id="password-error" class="text-red-400 text-sm mt-1 hidden">Passwords do not match.</p>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Sign Up</button>
            </form>

            <p class="mt-4 text-sm text-center text-gray-300">
                Already have an account? <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Sign
                    In</a>
            </p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#email").on("input", function() {
                let email = $(this).val();
                if (email.length > 3) {
                    $.ajax({
                        url: "{{ route('check.email') }}",
                        method: "POST",
                        data: {
                            email: email,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.exists) {
                                $("#email-error").removeClass("hidden");
                            } else {
                                $("#email-error").addClass("hidden");
                            }
                        }
                    });
                }
            });

            $("#confirm-password").on("input", function() {
                if ($(this).val() !== $("#password").val()) {
                    $("#password-error").removeClass("hidden");
                } else {
                    $("#password-error").addClass("hidden");
                }
            });
        });

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
        $(document).ready(function() {
            // Validasi panjang password minimal 6 karakter
            $("#password").on("input", function() {
                let password = $(this).val();
                if (password.length < 6) {
                    $("#password-length-error").removeClass("hidden");
                } else {
                    $("#password-length-error").addClass("hidden");
                }
            });

            $("#confirm-password").on("input", function() {
                if ($(this).val() !== $("#password").val()) {
                    $("#password-error").removeClass("hidden");
                } else {
                    $("#password-error").addClass("hidden");
                }
            });
        });
    </script>
</body>

</html>
