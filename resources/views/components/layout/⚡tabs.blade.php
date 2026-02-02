<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
<nav class="bg-white border-t fixed bottom-0 w-full left-1/2 -translate-x-1/2 z-50 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
     style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="flex justify-around items-center px-2 py-1">
        <!-- Home -->
        <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 group">
            <div class="p-2 {{ request()->is('/') ? 'bg-blue-600 text-white' : 'text-gray-400 group-hover:bg-gray-100' }} rounded-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path d="M3 13h1v7c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-7h1c.4 0 .77-.24.92-.62.15-.37.07-.8-.22-1.09l-8.99-9a.996.996 0 0 0-1.41 0l-9.01 9c-.29.29-.37.72-.22 1.09s.52.62.92.62Zm9-8.59 6 6V20H6v-9.59z"></path>
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ request()->is('/') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">Trang chủ</span>
        </a>

        <!-- Batteries -->
        <a href="{{ url('/batteries') }}" class="flex flex-col items-center gap-1 group">
            <div class="p-2 {{ request()->is('batteries*') ? 'bg-blue-600 text-white' : 'text-gray-400 group-hover:bg-gray-100' }} rounded-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ request()->is('batteries*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">Bình điện</span>
        </a>

        <!-- History -->
        <a href="{{ url('/history') }}" class="flex flex-col items-center gap-1 group">
            <div class="p-2 {{ request()->is('history*') ? 'bg-blue-600 text-white' : 'text-gray-400 group-hover:bg-gray-100' }} rounded-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ request()->is('history*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">Lịch sử</span>
        </a>

        <!-- Account -->
        <a href="{{ url('/user') }}" class="flex flex-col items-center gap-1 group">
            <div class="p-2 {{ request()->is('user*') ? 'bg-blue-600 text-white' : 'text-gray-400 group-hover:bg-gray-100' }} rounded-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <span class="text-[10px] font-bold {{ request()->is('user*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}">Tài khoản</span>
        </a>
    </div>
</nav>

</div>