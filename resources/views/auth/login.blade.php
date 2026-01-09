<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | SIMSDM Yayasan Indonesia Khatam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0d2466',
                        /* Biru Dongker */
                        accent: '#ff6b35',
                        /* Orange */
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fix background kuning autofill chrome */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }

        .logo-pop {
            animation: pop 0.4s ease-out;
        }

        @keyframes pop {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-2 fade-in-up">

        <div class="hidden md:flex flex-col justify-between bg-primary relative overflow-hidden text-white p-8">

            <div class="absolute top-0 left-0 w-40 h-40 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
            <div class="absolute bottom-0 right-0 w-40 h-40 bg-accent opacity-20 rounded-full translate-x-1/3 translate-y-1/3 blur-2xl"></div>

            <div class="relative z-10 mt-4">
                <p class="text-xs font-semibold tracking-[0.2em] uppercase text-gray-300 mb-2">Internal System</p>
                <h1 class="text-3xl font-bold leading-tight">
                    Yayasan <br>
                    <span class="text-accent">Indonesia Khatam</span>
                </h1>
            </div>

            <div class="relative z-10 mb-4">
                <p class="text-gray-300 text-xs leading-relaxed opacity-80">
                    Sistem Informasi Manajemen Sumber Daya Manusia (SIMSDM) Terintegrasi.
                </p>
            </div>
        </div>

        <div class="p-8 md:p-10 flex flex-col justify-center bg-white relative">

            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 mb-3 shadow-sm ring-4 ring-white">
                    <img
                        src="{{ asset('assets/img/logo.png') }}"
                        alt="Logo Yayasan Indonesia Khatam"
                        class="w-10 h-10 object-contain">
                </div>

                <h2 class="text-xl font-bold text-gray-800">Selamat Datang Kembali</h2>
                <p class="text-gray-500 text-xs mt-1">Masukkan kredensial Anda untuk masuk.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" name="email" required autofocus placeholder="email@yayasan.com"
                            class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-gray-400">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs mt-2">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2">Ingat Saya</span>
                    </label>
                    <a href="#" class="font-medium text-primary hover:text-accent transition">Lupa Password?</a>
                </div>

                @if ($errors->any())
                <div class="p-2.5 rounded-lg bg-red-50 text-red-600 text-xs border border-red-100 flex items-center">
                    <span class="font-bold mr-1">Oops!</span> {{ $errors->first() }}
                </div>
                @endif

                <button type="submit"
                    class="w-full bg-primary hover:bg-[#0a1b4d] text-white font-semibold py-2.5 rounded-lg shadow-md hover:shadow-lg transform transition active:scale-95 text-sm mt-2">
                    Masuk
                </button>

            </form>

            <div class="mt-6 text-center">
                <p class="text-[10px] text-gray-400">
                    © {{ date('Y') }} SIMSDM - Yayasan Indonesia Khatam
                </p>
            </div>
        </div>

    </div>

</body>

</html>