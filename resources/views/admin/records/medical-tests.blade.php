@extends('admin.layouts.app')

@section('title', 'التحاليل المخبرية')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-vial text-purple-600 ml-2"></i>
        التحاليل المخبرية
    </h1>
    <p class="text-gray-600 mt-1">إجمالي: {{ $records->total() }} تحليل</p>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-purple-500 to-pink-600">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">سكر الدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">CK-MB</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">Troponin</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التشخيص AI</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($records as $test)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $test->id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.users.show', $test->user) }}" class="flex items-center hover:text-indigo-600">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($test->user->name, 0, 1) }}
                            </div>
                            <span class="mr-3 text-sm font-medium">{{ $test->user->name }}</span>
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-gray-800">{{ $test->blood_sugar }}</span>
                        <span class="text-xs text-gray-500">mg/dL</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-gray-800">{{ $test->ck_mb }}</span>
                        <span class="text-xs text-gray-500">U/L</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="font-semibold text-gray-800">{{ $test->troponin }}</span>
                        <span class="text-xs text-gray-500">ng/mL</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($test->diagnosis)
                            @if($test->diagnosis->result == 'positive')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle"></i> إيجابي
                                </span>
                                <p class="text-xs text-gray-600 mt-1">
                                    احتمال: {{ number_format($test->diagnosis->confidence_score * 100, 1) }}%
                                </p>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle"></i> سلبي
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">لا يوجد</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $test->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-flask text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500">لا توجد تحاليل</p>
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
