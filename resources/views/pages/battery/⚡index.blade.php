<?php

use App\Models\Battery;
use App\Models\BatteryUsage;
use App\Models\ForkLift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public $forklifts = [];

    public $availableBatteries = [];

    public $search = '';

    public $batterySearch = '';

    // Modal states
    public $showInstallModal = false;

    public $showRemoveModal = false;

    // Form data
    public $selectedForkliftId = null;

    public $selectedUsageId = null;

    // Install form
    public $installForm = [
        'battery_id' => '',
        'charger_bar' => '',
        'screen_bar' => '',
    ];

    // Remove form
    public $removeForm = [
        'hour_in' => '', // Display only
    ];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->forklifts = ForkLift::with(['activeUsages.battery'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%')
                    ->orWhere('brand', 'like', '%'.$this->search.'%');
            })
            ->get();
        // Get batteries that are NOT currently 'in_use'
        $this->availableBatteries = Battery::where('status', '!=', 'in_use')
            ->when($this->batterySearch, function ($query) {
                $query->where('code', 'like', '%'.$this->batterySearch.'%')
                    ->orWhere('type', 'like', '%'.$this->batterySearch.'%');
            })
            ->get();
    }

    public function updatedSearch()
    {
        $this->refreshData();
    }

    public function updatedBatterySearch()
    {
        $this->refreshData();
    }

    // --- Install Actions ---

    public function openInstallModal($forkliftId)
    {
        $this->selectedForkliftId = $forkliftId;
        $this->reset('installForm');
        $this->showInstallModal = true;
    }

    public function installBattery()
    {
        $this->validate([
            'installForm.battery_id' => 'required|exists:batteries,id',
            'installForm.charger_bar' => 'required',
            'installForm.screen_bar' => 'required',
        ], [
            'installForm.battery_id.required' => 'Vui lòng chọn một bình pin.',
            'installForm.battery_id.exists' => 'Bình pin đã chọn không hợp lệ.',
            'installForm.charger_bar.required' => 'Vui lòng chọn vạch pin nạp.',
            'installForm.screen_bar.required' => 'Vui lòng chọn vạch màn hình.',
        ]);

        $forklift = ForkLift::find($this->selectedForkliftId);
        if (! $forklift) {
            return;
        }

        $battery = Battery::find($this->installForm['battery_id']);
        if (! $battery) {
            return;
        }

        // Create Usage
        BatteryUsage::create([
            'fork_lift_id' => $this->selectedForkliftId,
            'battery_id' => $this->installForm['battery_id'],
            'hour_initial' => $battery->total_working_hours, // Automatically use battery hours
            'charger_bar' => $this->installForm['charger_bar'],
            'screen_bar' => $this->installForm['screen_bar'],
            'installed_at' => now(),
            'installed_by' => Auth::id() ?? 1,
            'status' => 'running',
        ]);

        // Update Battery Status
        $battery->update(['status' => 'in_use']);

        $this->showInstallModal = false;
        $this->refreshData();
    }

    // --- Remove Actions ---

    public function openRemoveModal($usageId, $hourIn)
    {
        $this->selectedUsageId = $usageId;
        $this->removeForm['hour_in'] = $hourIn;
        $this->showRemoveModal = true;
    }

    public function removeBattery()
    {
        $usage = BatteryUsage::with('battery')->find($this->selectedUsageId);
        if (! $usage) {
            return;
        }

        $now = now();
        $installedAt = \Carbon\Carbon::parse($usage->installed_at);
        $diffSeconds = abs(now()->getTimestamp() - \Carbon\Carbon::parse($usage->installed_at)->getTimestamp());
        $workingHours = round($diffSeconds / 3600, 2);
        $hourOut = (float) $usage->hour_initial + (float) $workingHours;

        $usage->update([
            'hour_out' => $hourOut,
            'removed_at' => $now,
            'removed_by' => Auth::id() ?? 1,
            'working_hours' => $workingHours,
            'status' => 'finished',
        ]);

        // Update Battery Total Hours & Status
        $usage->battery->update([
            'status' => 'standby',
            'total_working_hours' => $hourOut,
        ]);

        $this->showRemoveModal = false;
        $this->refreshData();
    }
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
};
?>

<div class="p-2 animate-page-entry">
    <div class="mb-6 flex flex-col gap-4">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Quản lý thay bình sạc</h1>
            <p class="text-sm text-gray-500 font-medium text-left sm:text-left">Danh sách các xe nâng và bình sạc sử dụng</p>
        </div>

        <!-- Search Input -->
        <div class="relative group">
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                placeholder="Tìm kiếm mã xe, tên hoặc hãng..." 
                class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-2xl bg-white shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all"
            >
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Forklift List with Loading State -->
    <div class="relative min-h-[400px]">
        <!-- Loading Spinner -->
        <div wire:loading wire:target="search" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-gray-100/50 backdrop-blur-[1px] rounded-2xl transition-all">
            <div class="flex flex-col items-center gap-3 p-6 bg-white rounded-2xl shadow-xl border border-gray-100">
                <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-600">Đang tìm kiếm...</span>
            </div>
        </div>

        <!-- Grid -->
        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="search" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 transition-opacity">
            @forelse($forklifts as $forklift)
                @php $isActive = $forklift->activeUsages->isNotEmpty(); @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col justify-between h-full relative overflow-hidden transition-all hover:shadow-md">
                    <!-- Status Bar -->
                    <div class="absolute top-0 left-0 w-full h-1 {{ $isActive ? 'bg-linear-to-r from-green-400 to-green-600' : 'bg-linear-to-r from-red-400 to-red-500' }}"></div>

                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $forklift->code ?? $forklift->name }}</h3>
                                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ $forklift->brand }}</span>
                            </div>
                            @if($isActive)
                                <span class="px-2.5 py-1 bg-green-50 text-green-700 text-[10px] font-bold rounded-full border border-green-100 uppercase tracking-tighter">Đang sử dụng ({{ $forklift->activeUsages->count() }})</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-50 text-gray-500 text-[10px] font-bold rounded-full border border-gray-100 uppercase tracking-tighter">Trống</span>
                            @endif
                        </div>
        
                        @if($isActive)
                            <div class="space-y-2.5">
                                @foreach($forklift->activeUsages as $usage)
                                    <!-- Active Battery Info -->
                                    <div class="bg-blue-50/50 rounded-xl p-3 border border-blue-100/50 group relative hover:bg-blue-50 transition-colors shadow-xs">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 bg-blue-500 rounded-lg text-white shadow-sm shadow-blue-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                                    </svg>
                                                </div>
                                                <span class="font-black text-blue-900 text-sm tracking-tight">Bình: {{ $usage->battery->code ?? 'N/A' }}</span>
                                            </div>
                                            <button 
                                                wire:click="openRemoveModal({{ $usage->id }}, {{ $usage->hour_initial }})"
                                                class="text-[10px] bg-white hover:bg-red-50 text-red-600 px-3 py-1.5 rounded-lg border border-red-100 font-black shadow-xs transition-all active:scale-95"
                                            >
                                                THÁO
                                            </button>
                                        </div>

                                        <!-- Installer & Duration Info -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-1.5 overflow-hidden">
                                                @if($usage->installedBy->avatar)
                                                    <img src="{{ $usage->installedBy->avatar }}" class="w-5 h-5 rounded-full object-cover border border-blue-200" alt="Installer">
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-[10px] text-blue-600 font-black shrink-0">
                                                        {{ substr($usage->installedBy->name ?? '?', 0, 1) }}
                                                    </div>
                                                @endif
                                                <span class="text-[10px] text-blue-800/60 font-bold truncate">{{ $usage->installedBy->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 bg-white/80 px-2 py-0.5 rounded-full border border-blue-100/50 shadow-xs">
                                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                                <span class="text-[9px] font-black text-blue-900">{{ $this->formatDuration(abs(now()->getTimestamp() - \Carbon\Carbon::parse($usage->installed_at)->getTimestamp())) }}</span>
                                            </div>
                                        </div>

                                        <!-- Bottom Stats Grid -->
                                        <div class="grid grid-cols-3 gap-2 py-2 border-t border-blue-100/30">
                                            <div class="flex flex-col">
                                                <span class="text-[8px] text-blue-800/40 uppercase font-black tracking-widest">Giờ vào</span>
                                                <span class="text-[10px] font-bold text-blue-900 leading-tight tabular-nums">{{ $usage->hour_initial }}h</span>
                                            </div>
                                            <div class="flex flex-col border-x border-blue-100/30 px-2">
                                                <span class="text-[8px] text-blue-800/40 uppercase font-black tracking-widest">Vạch sạc</span>
                                                <span class="text-[10px] font-bold text-blue-900 leading-tight">{{ $usage->charger_bar }} vạch</span>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="text-[8px] text-blue-800/40 uppercase font-black tracking-widest">Màn hình</span>
                                                <span class="text-[10px] font-bold text-blue-900 leading-tight">{{ $usage->screen_bar }} %</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- No Battery State -->
                            <div class="bg-gray-50/50 rounded-xl p-3 mb-4 border border-gray-100 text-center py-5 border-dashed">
                                <span class="text-gray-400 text-xs font-medium italic">Sẵn sàng lắp bình</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button 
                            wire:click="openInstallModal({{ $forklift->id }})"
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm active:scale-[0.98] flex items-center justify-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Lắp Thêm Bình
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-2xl border-2 border-dashed border-gray-100 flex flex-col items-center justify-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3 opacity-20">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <p class="text-sm font-medium">Không tìm thấy xe nâng phù hợp</p>
                    <button wire:click="$set('search', '')" class="mt-2 text-blue-500 text-xs font-bold hover:underline">Xóa tìm kiếm</button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Install Modal -->
    @if($showInstallModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
            <h3 class="text-lg font-bold mb-4">Lắp Bình Vào Xe</h3>
            
            <div class="space-y-4 max-h-[80vh] overflow-y-auto pr-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Chọn Bình Có Sẵn</label>
                    
                    <!-- Battery Search inside Modal -->
                    <div class="relative mb-3">
                        <input 
                            wire:model.live.debounce.300ms="batterySearch"
                            type="text" 
                            placeholder="Tìm mã bình hoặc loại..." 
                            class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-300 text-xs transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 max-h-[320px] overflow-y-auto p-1 custom-scrollbar">
                        @forelse($availableBatteries as $bat)
                            <button 
                                wire:click="$set('installForm.battery_id', {{ $bat->id }})"
                                class="flex items-center justify-between p-3 border rounded-xl transition-all {{ $installForm['battery_id'] == $bat->id ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-100 hover:border-gray-300 bg-white shadow-sm' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 {{ $installForm['battery_id'] == $bat->id ? 'bg-blue-100 text-blue-600' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-black text-gray-900 leading-none mb-1">{{ $bat->code }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">{{ $bat->type }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-400 font-bold uppercase mb-0.5">Số giờ</div>
                                    <div class="text-sm font-black text-gray-700 leading-none">{{ number_format($bat->total_working_hours, 2) }}h</div>
                                </div>
                            </button>
                        @empty
                            <div class="py-10 text-center text-gray-400 text-xs italic">Không tìm thấy bình phù hợp</div>
                        @endforelse
                    </div>
                    @error('installForm.battery_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    @php
                        $selectedBat = $availableBatteries->firstWhere('id', $installForm['battery_id']);
                    @endphp
                    @if($selectedBat)
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex justify-between items-center">
                            <div>
                                <div class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">Số giờ hiện tại</div>
                                <div class="text-xl font-black text-blue-900">{{ number_format($selectedBat->total_working_hours, 2) }}h</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">Mã bình</div>
                                <div class="text-sm font-bold text-blue-900">{{ $selectedBat->code }}</div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 text-center text-sm text-gray-400">
                            Vui lòng chọn bình để xem số giờ
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <!-- Charger Bar Slider -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-bold text-gray-700">Vạch Pin Nạp (Máy Nạp)</label>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg font-bold text-sm">
                                <span x-text="$wire.installForm.charger_bar || 0"></span> vạch
                            </span>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <span class="text-xs font-bold text-gray-400">1</span>
                            <input type="range" min="1" max="4" step="1" 
                                wire:model.live="installForm.charger_bar" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600 focus:outline-none"
                            >
                            <span class="text-xs font-bold text-gray-400">4</span>
                        </div>
                        @error('installForm.charger_bar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Screen Bar Slider -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-bold text-gray-700">Vạch Màn Hình (%)</label>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg font-bold text-sm">
                                <span x-text="$wire.installForm.screen_bar || 0"></span>%
                            </span>
                        </div>
                        <div class="relative flex items-center gap-4">
                            <span class="text-xs font-bold text-gray-400">0%</span>
                            <input type="range" min="0" max="100" step="1" 
                                wire:model.live="installForm.screen_bar" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600 focus:outline-none"
                            >
                            <span class="text-xs font-bold text-gray-400">100%</span>
                        </div>
                        @error('installForm.screen_bar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button wire:click="$set('showInstallModal', false)" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 text-sm font-bold rounded-xl">Hủy</button>
                <button wire:click="installBattery" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl">Lưu Lắp Đặt</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Remove Modal -->
    @if($showRemoveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
            <h3 class="text-lg font-bold mb-4">Tháo Bình Ra</h3>
            
            <div class="space-y-4">
                @php
                    $usage = BatteryUsage::with(['battery', 'forkLift'])->find($selectedUsageId);
                @endphp
                @if($usage)
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 rounded-xl text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium">Đang tháo bình</div>
                            <div class="text-sm font-bold text-gray-900">{{ $usage->battery->code }} - {{ $usage->forkLift->code ?? $usage->forkLift->name }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Giờ lúc vào</div>
                            <div class="text-lg font-bold text-gray-700">{{ $usage->hour_initial }}h</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Số giờ sử dụng</div>
                            <div class="text-lg font-bold text-blue-600">{{ $this->formatDuration(abs(now()->getTimestamp() - \Carbon\Carbon::parse($usage->installed_at)->getTimestamp())) }}</div>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 text-center px-4">Xác nhận tháo bình? Hệ thống sẽ tự động tính toán tổng số giờ làm việc dựa trên thời gian thực tế.</p>
                @endif
            </div>

            <div class="mt-6 flex flex-col gap-2">
                <button wire:click="removeBattery" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-200 transition-all active:scale-[0.98]">Xác Nhận Tháo</button>
                <button wire:click="$set('showRemoveModal', false)" class="w-full py-3 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition-all">Quay lại</button>
            </div>
        </div>
    </div>
    @endif
</div>