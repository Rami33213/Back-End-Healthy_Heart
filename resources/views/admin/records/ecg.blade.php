@extends('admin.layouts.app')

@section('title', 'سجلات ECG')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-wave-square text-pink-600 ml-2"></i>
            سجلات تخطيط القلب (ECG)
        </h1>
        <p class="text-gray-600 mt-1">إجمالي: {{ $records->total() }} سجل</p>
    </div>
</div>

<!-- Records Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-pink-500 to-rose-600">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">النتيجة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الثقة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التشخيص</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التاريخ</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($records as $ecg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        {{ $ecg->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-rose-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($ecg->user->name, 0, 1) }}
                            </div>
                            <div class="mr-3">
                                <p class="text-sm font-medium text-gray-900">{{ $ecg->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ecg->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($ecg->result == 'normal')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle ml-1"></i> طبيعي
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle ml-1"></i> غير طبيعي
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($ecg->confidence_score)
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 ml-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $ecg->confidence_score * 100 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold">{{ number_format($ecg->confidence_score * 100, 1) }}%</span>
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($ecg->diagnosis)
                            <p class="font-medium text-gray-800">{{ Str::limit($ecg->diagnosis->result, 30) }}</p>
                        @else
                            <span class="text-gray-400">لا يوجد</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ecg->created_at->format('Y-m-d') }}
                        <br>
                        <span class="text-xs text-gray-400">{{ $ecg->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('admin.users.show', $ecg->user) }}" 
                           class="text-indigo-600 hover:text-indigo-900 mx-1"
                           title="عرض المستخدم">
                            <i class="fas fa-user"></i>
                        </a>
                        @if($ecg->file_path)
                            <a href="{{ asset('storage/' . $ecg->file_path) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-900 mx-1"
                               title="عرض الملف">
                                <i class="fas fa-file-image"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500">لا توجد سجلات ECG</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $records->links() }}
</div>

@endsection
