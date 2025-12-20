@extends('admin.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-users text-indigo-600 ml-2"></i>
            إدارة المستخدمين
        </h1>
        <p class="text-gray-600 mt-1">إجمالي: {{ $users->total() }} مستخدم</p>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="البحث بالاسم، البريد، أو رقم الهاتف..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
        </div>
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-search ml-2"></i>
            بحث
        </button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
        @endif
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-indigo-600 to-purple-600">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">المستخدم</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">البريد</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الهاتف</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">الجنس</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase">التسجيل</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $user->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="mr-3 text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $user->phone }}</td>
                    <td class="px-6 py-4">
                        @if($user->profile && $user->profile->gender)
                            @if($user->profile->gender == 'male')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-mars"></i> ذكر
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">
                                    <i class="fas fa-venus"></i> أنثى
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="px-3 py-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition"
                               title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('⚠️ هل أنت متأكد من حذف {{ $user->name }}؟\n\nسيتم حذف جميع سجلاته الطبية!');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                        title="حذف المستخدم">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-users-slash text-6xl text-gray-300 mb-4"></i>
                        <p class="text-xl text-gray-500">لا يوجد مستخدمين</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>

@endsection
