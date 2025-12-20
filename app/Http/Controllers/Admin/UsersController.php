<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'profile',
            'ecgRecords',
            'heartRateRecords',
            'medicalTests',
            'expertConsultations',
            'diagnosis',
            'auditLogs'
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        try {
            $userName = $user->name;
            $userId = $user->id;
            
            // حذف المستخدم (cascade سيحذف كل السجلات المرتبطة)
            $user->delete();
            
            // تسجيل العملية
            AuditLogger::logAdmin('Deleted user', "User: {$userName} (ID: {$userId})");
            
            return redirect()->route('admin.users.index')
                ->with('success', "تم حذف المستخدم {$userName} بنجاح");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'فشل حذف المستخدم: ' . $e->getMessage());
        }
    }
}
