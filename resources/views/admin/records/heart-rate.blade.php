@extends('admin.layouts.app')

@section('title', 'قياسات معدل ضربات القلب')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-heart-pulse text-blue-600 ml-2"></i>
        قياسات معدل ضربات القلب
    </h1>
    <p class="text-gray-600 mt-1">إجمالي: {{ $records->total() }} قياس</p>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-blue-500 to-cyan-600">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">معدل النبض</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الطريقة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التاريخ</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($records as $hr)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $hr->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($hr->user->name, 0, 1) }}
                            </div>
                            <div class="mr-3">
                                <p class="text-sm font-medium text-gray-900">{{ $hr->user->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-2xl font-bold text-blue-600">{{ $hr->heart_rate_value }}</span>
                        <span class="text-sm text-gray-500">BPM</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $status = $hr->heart_rate_value < 60 ? 'low' : ($hr->heart_rate_value > 100 ? 'high' : 'normal');
                        @endphp
                        @if($status == 'normal')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle"></i> طبيعي
                            </span>
                        @elseif($status == 'low')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-arrow-down"></i> منخفض
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-arrow-up"></i> مرتفع
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                            {{ ucfirst($hr->method) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $hr->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.users.show', $hr->user) }}" 
                           class="text-indigo-600 hover:text-indigo-900"
                           title="عرض المستخدم">
                            <i class="fas fa-user"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-heartbeat text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500">لا توجد قياسات</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $records->links() }}
</div>

@endsection
