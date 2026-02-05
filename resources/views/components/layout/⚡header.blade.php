<?php

use Livewire\Component;
 
new class extends Component {
    //
};

?>
<!-- Header (top cho cảm giác app mobile) -->
<div class="bg-blue-600 text-white w-full z-50 shadow-md fixed top-0" style="padding-top: env(safe-area-inset-top);">
    <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-2">
            @if(request()->is('/'))
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-200">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            @else
                <a href="{{ url('/') }}" class="text-white hover:text-blue-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
            @endif
            <h1 class="text-lg font-bold tracking-tight">Quản lý vận hành Pin sạc xe nâng</h1>
        </div>
        <div class="p-1 bg-blue-500 rounded-full text-[10px] font-bold uppercase tracking-wider">
            <a href="{{ url('/user') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </a>
        </div>
    </div>
</div>