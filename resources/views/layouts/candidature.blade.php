<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Candidature ECLA - AGAU Éditions')</title>

    <link rel="shortcut icon" href="{{ asset('images/AGAU LOGO BLEU.png') }}" type="image/x-icon">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Animations & Styles for Candidature */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }
        
        .step-item.active .step-icon {
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            transform: scale(1.1);
        }
        
        .step-item.completed .step-icon {
            background: #10b981;
            color: white;
        }

        .step-icon {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input {
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ff 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 min-h-screen flex flex-col">
    <x-navbar />

    <main class="flex-grow pt-24 pb-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

        <div class="max-w-6xl mx-auto relative z-10">
            <div class="mb-10 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-600 mb-4 font-montserrat">
                    Appel à Manuscrits ECLA
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto font-lora">
                    Rejoignez notre maison d'édition en soumettant votre œuvre. Nous sommes à la recherche de nouvelles voix littéraires.
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Progression -->
                <div class="w-full md:w-1/4">
                    <div class="glass-panel rounded-2xl p-6 sticky top-28">
                        <h3 class="text-xl font-bold mb-6 text-gray-800 font-montserrat flex items-center">
                            <i class="bi bi-map mr-2 text-blue-600"></i> Étapes
                        </h3>
                        <nav>
                            <ul class="space-y-6 relative">
                                
                                @php
                                    $currentStep = request()->route()->getName();
                                    $steps = [
                                        'candidature.step1' => ['icon' => 'bi-person', 'label' => 'Vos informations'],
                                        'candidature.step2' => ['icon' => 'bi-book', 'label' => 'L\'œuvre'],
                                        'candidature.step3' => ['icon' => 'bi-cloud-upload', 'label' => 'Manuscrit'],
                                        'candidature.step4' => ['icon' => 'bi-check2-square', 'label' => 'Engagement'],
                                        'candidature.confirmation' => ['icon' => 'bi-stars', 'label' => 'Confirmation'],
                                    ];
                                    
                                    $stepKeys = array_keys($steps);
                                    $currentIndex = array_search($currentStep, $stepKeys);
                                @endphp

                                @foreach($steps as $route => $info)
                                    @php
                                        $index = $loop->index;
                                        $status = '';
                                        if ($index < $currentIndex) $status = 'completed';
                                        elseif ($index == $currentIndex) $status = 'active';
                                    @endphp
                                    <li class="relative flex items-center gap-4 step-item {{ $status }}">
                                        <div class="step-icon h-10 w-10 rounded-full flex items-center justify-center bg-white border-2 border-slate-200 shadow-sm z-10 text-gray-400">
                                            @if($status == 'completed')
                                                <i class="bi bi-check-lg text-lg"></i>
                                            @else
                                                <i class="bi {{ $info['icon'] }} text-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block text-sm font-semibold {{ $status == 'active' ? 'text-blue-700' : ($status == 'completed' ? 'text-emerald-600' : 'text-gray-500') }}">
                                                {{ $info['label'] }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="w-full md:w-3/4">
                    <div class="glass-panel rounded-3xl p-8 md:p-10 shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full opacity-50 -z-10"></div>
                        @yield('candidature_content')
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-footer />
    @stack('scripts')
</body>
</html>
