<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-pink-500 p-8 text-center">
                <i class="fas fa-heartbeat text-6xl text-white mb-4"></i>
                <h1 class="text-3xl font-bold text-white">لوحة التحكم</h1>
                <p class="text-indigo-100 mt-2">نظام إدارة الرعاية القلبية</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                @if($errors->any())
                    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                        <p class="font-semibold">خطأ في تسجيل الدخول</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-envelope ml-2"></i>
                            البريد الإلكتروني
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                            placeholder="admin@example.com"
                            required
                        >
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock ml-2"></i>
                            كلمة المرور
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                            <span class="mr-2 text-gray-700">تذكرني</span>
                        </label>
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-pink-500 text-white font-bold py-4 rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition duration-300"
                    >
                        <i class="fas fa-sign-in-alt ml-2"></i>
                        تسجيل الدخول
                    </button>
                </form>
            </div>

        </div>

        <p class="text-center text-white mt-6 text-sm">
            © 2025 Cardiac Care System. جميع الحقوق محفوظة.
        </p>
    </div>

</body>
</html>
