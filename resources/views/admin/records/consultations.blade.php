@extends('admin.layouts.app')

@section('title', 'الاستشارات الخبيرة')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-stethoscope text-emerald-600 ml-2"></i>
        الاستشارات الخبيرة
    </h1>
    <p class="text-gray-600 mt-1">إجمالي: {{ $records->total() }} استشارة</p>
</div>

<div class="grid grid-cols-1 gap-4">
    @forelse($records as $consult)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($consult->user->name, 0, 1) }}
                    </div>
                    <div class="mr-4">
                        <a href="{{ route('admin.users.show', $consult->user) }}" class="text-lg font-bold text-gray-800 hover:text-indigo-600">
                            {{ $consult->user->name }}
                        </a>
                        <p class="text-sm text-gray-500">{{ $consult->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                
                @php
                    $riskColors = [
                        'high' => 'bg-red-100 text-red-800',
                        'medium' => 'bg-yellow-100 text-yellow-800',
                        'low' => 'bg-green-100 text-green-800',
                    ];
                @endphp
                <span class="px-4 py-2 text-sm font-bold rounded-full {{ $riskColors[$consult->risk_level] ?? 'bg-gray-100 text-gray-800' }}">
                    @if($consult->risk_level == 'high')
                        <i class="fas fa-exclamation-triangle"></i> خطر عالي
                    @elseif($consult->risk_level == 'medium')
                        <i class="fas fa-exclamation-circle"></i> خطر متوسط
                    @else
                        <i class="fas fa-check-circle"></i> خطر منخفض
                    @endif
                </span>
            </div>

            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-4 mb-4">
                <h4 class="font-bold text-emerald-900 mb-2">
                    <i class="fas fa-diagnoses ml-1"></i> التشخيص:
                </h4>
                <p class="text-emerald-800">{{ $consult->diagnosis_label }}</p>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <h4 class="font-bold text-blue-900 mb-2">
                    <i class="fas fa-lightbulb ml-1"></i> التوصية:
                </h4>
                <p class="text-blue-800">{{ $consult->recommendation }}</p>
            </div>

            @if($consult->symptoms && is_array($consult->symptoms))
            <div class="border-t pt-4">
                <h4 class="font-bold text-gray-700 mb-3">
                    <i class="fas fa-list-check ml-1"></i> الأعراض المبلغ عنها:
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($consult->symptoms as $symptom)
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                            <i class="fas fa-circle text-xs text-red-500"></i> {{ $symptom }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-user-doctor text-6xl text-gray-300 mb-4"></i>
        <p class="text-xl text-gray-500">لا توجد استشارات</p>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $records->links() }}
</div>

@endsection
