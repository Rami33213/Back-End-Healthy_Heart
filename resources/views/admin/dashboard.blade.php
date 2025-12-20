@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-chart-line text-indigo-600 ml-2"></i>
        لوحة التحكم
    </h1>
    <p class="text-gray-600 mt-1">مرحباً {{ auth('admin')->user()->name }}</p>
</div>

<!-- Stats Cards - محسّنة -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- المستخدمين -->
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm opacity-90">إجمالي المستخدمين</p>
                <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['total_users']) }}</h3>
                <p class="text-xs opacity-75 mt-2">
                    <i class="fas fa-user-check"></i> {{ $stats['active_users'] }} نشط
                </p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- ECG -->
    <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm opacity-90">سجلات ECG</p>
                <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['total_ecg']) }}</h3>
                <p class="text-xs opacity-75 mt-2">تخطيط القلب</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-wave-square text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Heart Rate -->
    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm opacity-90">قياسات القلب</p>
                <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['total_heart_rate']) }}</h3>
                <p class="text-xs opacity-75 mt-2">Heart Rate</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-heart-pulse text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- التشخيصات -->
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-shadow">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm opacity-90">التشخيصات AI</p>
                <h3 class="text-3xl font-bold mt-2">{{ number_format($stats['total_diagnosis']) }}</h3>
                <p class="text-xs opacity-75 mt-2">بالذكاء الاصطناعي</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-brain text-2xl"></i>
            </div>
        </div>
    </div>

</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.users.index') }}" class="bg-white rounded-xl p-5 shadow hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center">
            <div class="bg-indigo-100 rounded-lg p-3 ml-4">
                <i class="fas fa-users text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">إدارة المستخدمين</h4>
                <p class="text-sm text-gray-500">عرض وإدارة الحسابات</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.records.ecg') }}" class="bg-white rounded-xl p-5 shadow hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center">
            <div class="bg-pink-100 rounded-lg p-3 ml-4">
                <i class="fas fa-heartbeat text-pink-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">السجلات الطبية</h4>
                <p class="text-sm text-gray-500">ECG والقياسات</p>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.records.consultations') }}" class="bg-white rounded-xl p-5 shadow hover:shadow-lg transition-all hover:-translate-y-1">
        <div class="flex items-center">
            <div class="bg-emerald-100 rounded-lg p-3 ml-4">
                <i class="fas fa-stethoscope text-emerald-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">الاستشارات</h4>
                <p class="text-sm text-gray-500">النظام الخبير</p>
            </div>
        </div>
    </a>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Recent Users -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
            <h3 class="text-lg font-bold text-white">
                <i class="fas fa-user-plus ml-2"></i>
                آخر المستخدمين
            </h3>
        </div>
        <div class="p-4">
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($recentUsers as $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="mr-3">
                            <p class="font-semibold text-gray-800 text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">لا يوجد مستخدمين</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-pink-500 to-rose-600 p-4">
            <h3 class="text-lg font-bold text-white">
                <i class="fas fa-history ml-2"></i>
                آخر النشاطات
            </h3>
        </div>
        <div class="p-4">
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($recentLogs as $log)
                <div class="flex items-start p-2 hover:bg-gray-50 rounded-lg transition">
                    <i class="fas fa-circle text-xs {{ $loop->first ? 'text-green-500' : 'text-gray-300' }} mt-1.5 ml-2"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">
                            {{ $log->user ? $log->user->name : 'نظام' }}
                        </p>
                        <p class="text-xs text-gray-600">{{ $log->action }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">لا توجد نشاطات</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
