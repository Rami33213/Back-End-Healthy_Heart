<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Cardiac Care</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body { background: #f1f5f9; }
        
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #94a3b8;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0.25rem 0;
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            transform: translateX(-3px);
        }

        .card {
            background: white;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
    </style>

    @stack('styles')
</head>
<body>
    
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        
        <!-- Sidebar -->
        <aside class="sidebar w-64 flex-shrink-0" x-show="sidebarOpen">
            <div class="flex flex-col h-full">
                
                <div class="flex items-center justify-center h-16 border-b border-gray-700">
                    <h1 class="text-xl font-bold text-white">
                        <i class="fas fa-heartbeat text-pink-500"></i>
                        Cardiac Admin
                    </h1>
                </div>

                <nav class="flex-1 overflow-y-auto p-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span class="mr-2">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users w-5"></i>
                        <span class="mr-2">المستخدمين</span>
                    </a>

                    <div class="mt-4 mb-2 px-3 text-xs font-semibold text-gray-500 uppercase">
                        السجلات الطبية
                    </div>

                    <a href="{{ route('admin.records.ecg') }}" 
                       class="nav-link {{ request()->routeIs('admin.records.ecg') ? 'active' : '' }}">
                        <i class="fas fa-wave-square w-5"></i>
                        <span class="mr-2">ECG</span>
                    </a>

                    <a href="{{ route('admin.records.heart-rate') }}" 
                       class="nav-link {{ request()->routeIs('admin.records.heart-rate') ? 'active' : '' }}">
                        <i class="fas fa-heart-pulse w-5"></i>
                        <span class="mr-2">Heart Rate</span>
                    </a>

                    <a href="{{ route('admin.records.medical-tests') }}" 
                       class="nav-link {{ request()->routeIs('admin.records.medical-tests') ? 'active' : '' }}">
                        <i class="fas fa-vial w-5"></i>
                        <span class="mr-2">التحاليل</span>
                    </a>

                    <a href="{{ route('admin.records.consultations') }}" 
                       class="nav-link {{ request()->routeIs('admin.records.consultations') ? 'active' : '' }}">
                        <i class="fas fa-stethoscope w-5"></i>
                        <span class="mr-2">الاستشارات</span>
                    </a>

                    <a href="{{ route('admin.records.diagnosis') }}" 
                       class="nav-link {{ request()->routeIs('admin.records.diagnosis') ? 'active' : '' }}">
                        <i class="fas fa-brain w-5"></i>
                        <span class="mr-2">التشخيصات</span>
                    </a>
                </nav>

                <div class="border-t border-gray-700 p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-shield text-white text-sm"></i>
                            </div>
                            <div class="mr-2">
                                <p class="text-xs font-semibold text-white">{{ auth('admin')->user()->name }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300" title="تسجيل الخروج">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white shadow h-16 flex items-center px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="mr-auto text-right">
                    <p class="text-sm font-semibold">{{ now()->locale('ar')->isoFormat('dddd') }}</p>
                    <p class="text-xs text-gray-500">{{ now()->locale('ar')->isoFormat('D MMMM YYYY') }}</p>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                
                @if(session('success'))
                    <div class="bg-green-50 border-r-4 border-green-500 text-green-800 p-4 rounded-lg mb-6">
                        <p class="font-bold"><i class="fas fa-check-circle"></i> نجح!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-lg mb-6">
                        <p class="font-bold"><i class="fas fa-times-circle"></i> خطأ!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')
</body>
</html>
