<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reset Password - Pharmacy Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-3 sm:p-6 lg:p-10">

    <!-- Main Container Card -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 min-h-[640px] border border-slate-200/60">

        <!-- Left Hero Side (Teal Theme Banner) -->
        <div class="md:col-span-5 bg-gradient-to-b from-[#e6f4f1] via-[#d4eee8] to-[#bce2d9] p-8 md:p-10 flex flex-col justify-between items-center text-center relative overflow-hidden">
            
            <div class="absolute -top-24 -left-24 w-60 h-60 bg-[#008080]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-[#0d9488]/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 w-full pt-4 flex flex-col items-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg shadow-[#008080]/15 mb-6 border-4 border-white/80">
                    <svg class="w-11 h-11 text-[#008080]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9" class="stroke-[#008080]" stroke-width="1.8" />
                        <path d="M12 7v10M7 12h10" stroke-width="2.5" class="stroke-[#008080]" />
                        <path d="M15 9l2.5-2.5a2.121 2.121 0 0 1 3 3L18 12" stroke-width="1.8" class="stroke-[#0d9488]" />
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-[#004d40] tracking-wider uppercase">PHARMACY</h1>
                <p class="text-xs font-bold text-[#0d9488] tracking-[0.25em] uppercase mt-1">MANAGEMENT SYSTEM</p>

                <div class="flex items-center justify-center gap-2 my-4 w-full">
                    <div class="h-[1px] bg-[#008080]/20 w-12"></div>
                    <span class="text-[#008080] text-xs font-bold">+</span>
                    <div class="h-[1px] bg-[#008080]/20 w-12"></div>
                </div>

                <p class="text-sm text-slate-600 font-medium max-w-xs">Smart Pharmacy, Better Care</p>
            </div>

            <div class="relative z-10 w-full pb-4">
                <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-md px-4 py-2 rounded-full border border-white/80 shadow-sm text-xs font-semibold text-[#006666]">
                    <svg class="w-4 h-4 text-[#008080]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Secure Password Update</span>
                </div>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="md:col-span-7 p-8 md:p-12 lg:p-14 flex flex-col justify-center bg-white">
            <div class="max-w-md mx-auto w-full">
                
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Set New Password 🔑</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter your new password below to update your account.</p>
                </div>

                @if($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Account Email</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </span>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $email ?? '') }}"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-100 rounded-xl border border-slate-200 text-slate-600 text-sm outline-none cursor-not-allowed" 
                                   readonly>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">New Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </span>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50/50 rounded-xl border border-slate-200 focus:bg-white focus:border-[#008080] focus:ring-4 focus:ring-[#008080]/15 text-slate-800 placeholder-slate-400 text-sm outline-none transition" 
                                   required 
                                   placeholder="Enter new password (min 6 chars)">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm New Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </span>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50/50 rounded-xl border border-slate-200 focus:bg-white focus:border-[#008080] focus:ring-4 focus:ring-[#008080]/15 text-slate-800 placeholder-slate-400 text-sm outline-none transition" 
                                   required 
                                   placeholder="Confirm your new password">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#008080] hover:bg-[#006666] text-white py-3.5 rounded-xl font-semibold shadow-lg shadow-[#008080]/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer text-base active:scale-[0.99]">
                        <span>Update Password & Save</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 text-center pt-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#008080] hover:text-[#006666] hover:underline transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Back to Login</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
