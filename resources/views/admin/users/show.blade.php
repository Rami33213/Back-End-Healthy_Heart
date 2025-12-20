@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('content')

<div class="mb-8">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
        <i class="fas fa-arrow-right ml-2"></i>
        العودة للقائمة
    </a>
</div>

<!-- User Info Card -->
<div class="card p-8 mb-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center">
            <div class="w-24 h-24 bg-gradient-to-r from-indigo-500 to-pink-500 rounded-2xl flex items-center justify-center text-white text-4xl font-bold shadow-xl">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div class="mr-6">
                <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-envelope ml-2"></i>
                    {{ $user->email }}
                </p>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-phone ml-2"></i>
                    {{ $user->phone }}
                </p>
            </div>
        </div>
        <div class="text-left">
            <span class="badge badge-success text-sm">نشط</span>
            <p class="text-sm text-gray-500 mt-2">
                انضم في: {{ $user->created_at->format('Y-m-d') }}
            </p>
        </div>
    </div>

    @if($user->profile)
    <div class="mt-6 pt-6 border-t grid grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-500">الجنس</p>
            <p class="font-semibold text-gray-800 mt-1">
                {{ $user->profile->gender == 'male' ? 'ذكر' : 'أنثى' }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">تاريخ الميلاد</p>
            <p class="font-semibold text-gray-800 mt-1">
                {{ $user->profile->date_of_birth ? \Carbon\Carbon::parse($user->profile->date_of_birth)->format('Y-m-d') : 'غير محدد' }}
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">العنوان</p>
            <p class="font-semibold text-gray-800 mt-1">
                {{ $user->profile->address ?? 'غير محدد' }}
            </p>
        </div>
    </div>
    @endif
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">تسجيلات ECG</p>
                <h3 class="text-3xl font-bold text-indigo-600 mt-2">{{ $user->ecgRecords->count() }}</h3>
            </div>
            <i class="fas fa-wave-square text-4xl text-indigo-200"></i>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">قياسات القلب</p>
                <h3 class="text-3xl font-bold text-pink-600 mt-2">{{ $user->heartRateRecords->count() }}</h3>
            </div>
            <i class="fas fa-heart-pulse text-4xl text-pink-200"></i>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">التحاليل</p>
                <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ $user->medicalTests->count() }}</h3>
            </div>
            <i class="fas fa-vial text-4xl text-blue-200"></i>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">الاستشارات</p>
                <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $user->expertConsultations->count() }}</h3>
            </div>
            <i class="fas fa-stethoscope text-4xl text-green-200"></i>
        </div>
    </div>
</div>

<!-- Records Tabs -->
<div class="card overflow-hidden" x-data="{ tab: 'ecg' }">
    
    <!-- Tabs Header -->
    <div class="flex border-b bg-gray-50">
        <button @click="tab = 'ecg'" 
                :class="tab === 'ecg' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                class="px-6 py-4 font-semibold transition">
            <i class="fas fa-wave-square ml-2"></i>
            ECG Records
        </button>
        <button @click="tab = 'heart'" 
                :class="tab === 'heart' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                class="px-6 py-4 font-semibold transition">
            <i class="fas fa-heart-pulse ml-2"></i>
            Heart Rate
        </button>
        <button @click="tab = 'tests'" 
                :class="tab === 'tests' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                class="px-6 py-4 font-semibold transition">
            <i class="fas fa-vial ml-2"></i>
            Medical Tests
        </button>
        <button @click="tab = 'consultations'" 
                :class="tab === 'consultations' ? 'bg-white text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-800'"
                class="px-6 py-4 font-semibold transition">
            <i class="fas fa-stethoscope ml-2"></i>
            Consultations
        </button>
    </div>

    <!-- ECG Tab -->
    <div x-show="tab === 'ecg'" class="p-6">
        @if($user->ecgRecords->count() > 0)
            <div class="space-y-4">
                @foreach($user->ecgRecords as $ecg)
                <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">النتيجة: 
                                <span class="badge {{ $ecg->result == 'normal' ? 'badge-success' : 'badge-danger' }}">
                                    {{ $ecg->result == 'normal' ? 'طبيعي' : 'غير طبيعي' }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                Confidence: {{ $ecg->confidence_score ? number_format($ecg->confidence_score * 100, 2) . '%' : 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $ecg->created_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                        @if($ecg->diagnosis)
                        <div class="text-left">
                            <p class="text-sm text-gray-600">التشخيص:</p>
                            <p class="font-semibold text-indigo-600">{{ $ecg->diagnosis->result }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-12">لا توجد سجلات ECG</p>
        @endif
    </div>

    <!-- Heart Rate Tab -->
    <div x-show="tab === 'heart'" class="p-6">
        @if($user->heartRateRecords->count() > 0)
            <div class="space-y-4">
                @foreach($user->heartRateRecords as $hr)
                <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-pink-600">{{ $hr->heart_rate_value }} BPM</p>
                            <p class="text-sm text-gray-600 mt-1">Method: {{ ucfirst($hr->method) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $hr->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="text-left">
                            <span class="badge {{ $hr->status == 'normal' ? 'badge-success' : 'badge-warning' }}">
                                {{ $hr->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-12">لا توجد قياسات</p>
        @endif
    </div>

    <!-- Medical Tests Tab -->
    <div x-show="tab === 'tests'" class="p-6">
        @if($user->medicalTests->count() > 0)
            <div class="space-y-4">
                @foreach($user->medicalTests as $test)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Blood Sugar</p>
                            <p class="font-bold text-gray-800">{{ $test->blood_sugar }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">CK-MB</p>
                            <p class="font-bold text-gray-800">{{ $test->ck_mb }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Troponin</p>
                            <p class="font-bold text-gray-800">{{ $test->troponin }}</p>
                        </div>
                    </div>
                    @if($test->diagnosis)
                    <div class="mt-3 pt-3 border-t">
                        <p class="text-sm">
                            <span class="font-semibold">التشخيص:</span>
                            <span class="badge {{ $test->diagnosis->result == 'positive' ? 'badge-danger' : 'badge-success' }}">
                                {{ $test->diagnosis->result }}
                            </span>
                            ({{ number_format($test->diagnosis->confidence_score * 100, 2) }}%)
                        </p>
                    </div>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">{{ $test->created_at->format('Y-m-d H:i') }}</p>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-12">لا توجد تحاليل</p>
        @endif
    </div>

    <!-- Consultations Tab -->
    <div x-show="tab === 'consultations'" class="p-6">
        @if($user->expertConsultations->count() > 0)
            <div class="space-y-4">
                @foreach($user->expertConsultations as $consult)
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="font-semibold text-gray-800 mb-2">{{ $consult->diagnosis_label }}</p>
                    <p class="text-sm text-gray-600 mb-3">{{ $consult->recommendation }}</p>
                    <div class="flex items-center justify-between">
                        <span class="badge badge-{{ $consult->risk_level == 'high' ? 'danger' : ($consult->risk_level == 'medium' ? 'warning' : 'success') }}">
                            {{ ucfirst($consult->risk_level) }} Risk
                        </span>
                        <p class="text-xs text-gray-400">{{ $consult->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 py-12">لا توجد استشارات</p>
        @endif
    </div>

</div>

<!-- Activity Logs -->
<div class="card p-6 mt-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-history text-indigo-600 ml-2"></i>
        سجل النشاطات
    </h3>
    <div class="space-y-2 max-h-96 overflow-y-auto">
        @foreach($user->auditLogs()->latest()->take(20)->get() as $log)
        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-circle text-xs {{ $loop->first ? 'text-green-500' : 'text-gray-300' }} ml-3"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800">{{ $log->action }}</p>
                @if($log->description)
                    <p class="text-xs text-gray-600">{{ $log->description }}</p>
                @endif
            </div>
            <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
