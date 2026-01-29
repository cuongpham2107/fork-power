<?php

use App\Models\BatteryUsage;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours} tiếng";
        }
        if ($minutes > 0 || empty($parts)) {
            $parts[] = "{$minutes} phút";
        }
        return implode(' ', $parts);
    }

    public function with()
    {
        return [
            'usages' => BatteryUsage::with(['forkLift', 'battery', 'installedBy', 'removedBy'])
                ->where('installed_by', Auth::id())
                ->orWhere('removed_by', Auth::id())
                ->orderBy('installed_at', 'desc')
                ->paginate(15),
        ];
    }
};
?>

<div class="space-y-6 pt-4 pb-12 px-4 animate-page-entry">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight text-left sm:text-left">Lịch Sử Của Bạn</h1>
        <p class="text-sm text-gray-500 font-medium text-left sm:text-left">Danh sách lượt thay bình bạn đã thực hiện</p>
    </div>

    <!-- History List -->
    <div class="space-y-4">
        @forelse($usages as $usage)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:border-blue-200 transition-colors">
                <div class="p-4 sm:p-5">
                    <!-- Top section: Forklift and Status -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex flex-col items-center justify-center border border-blue-100 shrink-0 shadow-sm">
                                <span class="text-[9px] font-black text-blue-400 leading-none uppercase tracking-widest">XE</span>
                                <span class="font-black text-blue-900 leading-none mt-1 uppercase text-[8px] tracking-tight text-center ">{{ $usage->forkLift->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 text-base leading-tight">Bình: {{ $usage->battery->code ?? 'N/A' }}</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">{{ $usage->forkLift->brand ?? 'Forklift' }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            @if($usage->status === 'running')
                                <div class="flex items-center gap-1.5 bg-green-50 px-3 py-1 rounded-full border border-green-100 shadow-sm">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                    </span>
                                    <span class="text-[10px] font-black text-green-700 uppercase tracking-widest">Đang chạy</span>
                                </div>
                            @else
                                <div class="bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Hoàn tất</span>
                                </div>
                            @endif
                            <div class="mt-1.5 flex items-center gap-1.5">
                                <span class="text-[11px] font-black text-gray-900 tabular-nums">{{ $usage->working_hours ?? '?' }}h</span>
                                <span class="text-[9px] font-bold text-gray-300 uppercase">Làm việc</span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle section: Stats Detail -->
                    <div class="grid grid-cols-2 gap-3 py-3 border-y border-gray-50 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-50 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Thời gian dùng</p>
                                <p class="text-xs font-bold text-gray-700">{{ $this->formatDuration($usage->working_hours * 3600) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-gray-50 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Giờ vào/ra</p>
                                <p class="text-xs font-bold text-gray-700 tracking-tight tabular-nums">{{ $usage->hour_initial }}h → {{ $usage->hour_out ?? '?' }}h</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom section: Personnel & Datetime -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col gap-2.5">
                            <!-- Người lắp -->
                            <div class="flex items-center gap-2">
                                @if($usage->installedBy->avatar)
                                    <img src="{{ $usage->installedBy->avatar }}" class="w-6 h-6 rounded-lg object-cover border border-gray-100" alt="Avatar">
                                @else
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 flex items-center justify-center text-[10px] text-blue-600 font-black shrink-0">
                                        {{ substr($usage->installedBy->name ?? '?', 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-gray-400 font-bold italic leading-none uppercase tracking-tighter">Lắp bởi:</span>
                                    <span class="text-[11px] font-bold text-gray-700 leading-tight">{{ $usage->installedBy->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <!-- Người tháo -->
                            @if($usage->removedBy)
                            <div class="flex items-center gap-2">
                                @if($usage->removedBy->avatar)
                                    <img src="{{ $usage->removedBy->avatar }}" class="w-6 h-6 rounded-lg object-cover border border-gray-100" alt="Avatar">
                                @else
                                    <div class="w-6 h-6 rounded-lg bg-red-100 flex items-center justify-center text-[10px] text-red-600 font-black shrink-0">
                                        {{ substr($usage->removedBy->name ?? '?', 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-gray-400 font-bold italic leading-none uppercase tracking-tighter">Tháo bởi:</span>
                                    <span class="text-[11px] font-bold text-gray-700 leading-tight">{{ $usage->removedBy->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-900 tracking-tight">{{ $usage->installed_at->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-medium italic mt-0.5">{{ $usage->installed_at->format('H:i') }} • {{ $usage->removed_at ? $usage->removed_at->format('H:i') : 'Đang chạy' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <div class="mx-auto w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-xs mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-400 font-bold italic">Chưa có lịch sử vận hành nào.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 px-2">
        {{ $usages->links() }}
    </div>
</div>