<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }
};
?>

<div class="p-4 animate-page-entry">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Cá Nhân</h1>
        <p class="text-sm text-gray-500 font-medium">Thông tin tài khoản của bạn</p>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" class="w-16 h-16 rounded-2xl object-cover shadow-lg shadow-blue-200" alt="Avatar">
            @else
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <span class="text-2xl font-black">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ Auth::user()->name }}</h2>
                <span class="text-sm text-gray-400 font-medium">{{ Auth::user()->username ?? Auth::user()->email }}</span>
            </div>
        </div>

        <div class="space-y-4">
            @if(Auth::user()->department_name)
            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                <span class="text-sm text-gray-400 font-medium italic">Phòng ban</span>
                <span class="text-sm font-bold text-gray-700 tracking-tight">{{ Auth::user()->department_name }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                <span class="text-sm text-gray-400 font-medium italic">Vai trò</span>
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-wider">Thành viên</span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                <span class="text-sm text-gray-400 font-medium italic">Ngày tham gia</span>
                <span class="text-sm text-gray-700 font-bold tracking-tight">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-3">
        {{-- <button class="w-full flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-xs hover:bg-gray-50 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 rounded-xl group-hover:bg-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-700">Đổi mật khẩu</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-gray-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </button> --}}

        <button wire:click="logout" class="w-full flex items-center justify-between p-4 bg-white rounded-2xl border border-red-100 shadow-xs hover:bg-red-50 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white rounded-xl group-hover:bg-red-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-red-600 ">Đăng xuất</span>
            </div>
        </button>
    </div>
</div>