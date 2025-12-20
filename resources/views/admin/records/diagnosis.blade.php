@extends('admin.layouts.app')

@section('title', 'التشخيصات')

@section('content')

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-brain text-indigo-600 ml-2"></i>
        التشخيصات بالذكاء الاصطناعي
    </h1>
    <p class="text-gray-600 mt-1">إجمالي: {{ $records->total() }} تشخيص</p>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-indigo-600 to-purple-600">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المصدر</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">النتيجة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الثقة</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($records as $diagnosis)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $diagnosis->id }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.users.show', $diagnosis->user) }}" class="flex items-center hover:text-indigo-600">
                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($diagnosis->user->name, 0, 1) }}
                            </div>
                            <span class="mr-3 text-sm font-medium">{{ $diagnosis->user->name }}</span>
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $sourceIcons = [
                                'ECGRecord' => 'fa-wave-square text-pink-600',
                                'MedicalTest' => 'fa-vial text-purple-600',
                                'HeartRateRecord' => 'fa-heart-pulse text-blue-600',
                            ];
                            $sourceNames = [
                                'ECGRecord' => 'ECG',
                                'MedicalTest' => 'تحليل مخبري',
                                'HeartRateRecord' => 'معدل النبض',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-sm">
                            <i class="fas {{ $sourceIcons[$diagnosis->source_type] ?? 'fa-file' }} ml-1"></i>
                            {{ $sourceNames[$diagnosis->source_type] ?? $diagnosis->source_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($diagnosis->result, 50) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($diagnosis->confidence_score)
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 ml-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $diagnosis->confidence_score * 100 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold">{{ number_format($diagnosis->confidence_score * 100, 1) }}%</span>
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $diagnosis->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <i class="fas fa-robot text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500">لا توجد تشخيصات</p>
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
