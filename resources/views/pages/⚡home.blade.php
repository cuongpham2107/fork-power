<?php
use Livewire\Component;
use App\Models\ForkLift;
use App\Models\Battery;
use App\Models\BatteryUsage;
 
new class extends Component {
    public $totalForklifts = 0;
    public $activeForklifts = 0;
    public $availableBatteries = 0;
    public $recentUsages = [];
    public $userSwaps = 0;
    public $chartLabels = [];
    public $chartValues = [];

    public function mount()
    {
        $this->totalForklifts = ForkLift::count();
        $this->activeForklifts = ForkLift::whereHas('activeUsages')->count();
        $this->availableBatteries = Battery::where('status', '!=', 'in_use')->count();
        $this->userSwaps = BatteryUsage::where('installed_by', Auth::id())->count();
        
        $this->recentUsages = BatteryUsage::with(['forkLift', 'battery', 'installedBy'])
            ->latest()
            ->take(5)
            ->get();

        // 7-day trend data
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $this->chartLabels[] = $date->format('d/m');
            $this->chartValues[] = BatteryUsage::whereDate('installed_at', $date->toDateString())->count();
        }
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
<div class="space-y-6 pt-4 pb-12 animate-page-entry px-4">
    <!-- Personalized Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Chào, {{ Auth::user()->name }} 👋</h1>
            <p class="text-sm text-gray-400 font-medium italic mt-0.5">{{ Auth::user()->department_name ?? 'Phòng vận hành' }}</p>
        </div>
        @if(Auth::user()->avatar)
            <img src="{{ Auth::user()->avatar }}" class="w-12 h-12 rounded-2xl object-cover shadow-lg shadow-blue-100 border-2 border-white" alt="Avatar">
        @else
            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <span class="text-xl font-black">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
        @endif
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-4">
        <!-- Main Stats -->
        <div class="col-span-2 bg-linear-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-xl shadow-blue-100 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-blue-100/80 text-xs font-black uppercase tracking-widest mb-1">Xe đang chạy</p>
                <h2 class="text-4xl font-black tabular-nums tracking-tighter">{{ $activeForklifts }}<span class="text-lg text-blue-200 font-medium ml-1">/ {{ $totalForklifts }}</span></h2>
                <div class="mt-4 flex items-center gap-2">
                    <div class="px-2 py-0.5 bg-white/20 rounded-full text-[10px] font-bold">Vận hành {{ round(($activeForklifts / max($totalForklifts, 1)) * 100) }}%</div>
                </div>
            </div>
            <!-- Decorative SVG background -->
            <svg class="absolute right-[-10%] bottom-[-20%] w-48 h-48 text-white/5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8.25 18.75h9m-9 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.129-1.125V14.25M7.5 14.25v-1.125c0-.621.504-1.125 1.125-1.125h9a1.125 1.125 0 0 1 1.125 1.125v1.125m-11.25 0h11.25"></path>
            </svg>
        </div>

        <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-100 flex flex-col items-center justify-between">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Bình điện trống</p>
            <h3 class="text-xl font-black text-gray-900">{{ $availableBatteries }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-100 flex flex-col items-center justify-between">
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Lượt thay của bạn</p>
            <h3 class="text-xl font-black text-blue-600">{{ $userSwaps }}</h3>
        </div>
    </div>

    <!-- Usage Trend Widget -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-black text-gray-900 tracking-tight">Xu hướng thay bình</h3>
            <span class="text-[10px] bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-black uppercase tracking-wider">7 ngày qua</span>
        </div>
        <div class="h-48 relative">
            <canvas id="usageTrendChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-black text-gray-900 tracking-tight">Lịch sử gần đây</h3>
            <a href="{{ url('/history') }}" class="text-[10px] text-blue-600 font-black uppercase tracking-widest hover:underline transition-all">Tất cả</a>
        </div>
        <div class="space-y-3">
            @forelse($recentUsages as $usage)
                <div class="bg-white p-4 rounded-2xl shadow-xs border border-gray-50 flex items-center justify-between hover:border-blue-100 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-gray-50 flex flex-col items-center justify-center border border-gray-100 shrink-0">
                            <span class="text-[10px] font-black text-gray-400 leading-none">XE</span>
                            <span class="font-black text-gray-900 leading-none mt-1 uppercase text-[8px] tracking-tight text-center">{{ $usage->forkLift->name ?? '?' }}</span>
                        </div>
                        <div>
                            <p class="font-black text-sm text-gray-900">Bình: {{ $usage->battery->code }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] text-gray-400 font-medium italic">{{ $usage->installedBy->name ?? 'N/A' }}</span>
                                <span class="text-[10px] text-gray-300">•</span>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $usage->installed_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($usage->status === 'running')
                            <div class="flex items-center gap-1.5 bg-green-50 px-2.5 py-1 rounded-full">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                </span>
                                <span class="text-[10px] font-black text-green-700 uppercase tracking-wider">Đang chạy</span>
                            </div>
                        @else
                            <p class="text-xs font-black text-gray-900 tabular-nums">{{ $usage->working_hours }}h</p>
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Hoàn tất</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                    <p class="text-sm text-gray-400 font-bold italic text-opacity-60">Chưa có hoạt động nào hôm nay</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chart Script -->
    @script
    <script>
        const canvasCtx = document.getElementById('usageTrendChart');
        if (canvasCtx) {
            new Chart(canvasCtx, {
                type: 'line',
                data: {
                    labels: $wire.chartLabels,
                    datasets: [{
                        label: 'Lượt thay',
                        data: $wire.chartValues,
                        borderColor: '#2563eb', // blue-600
                        backgroundColor: (context) => {
                            const chart = context.chart;
                            const {ctx, chartArea} = chart;
                            if (!chartArea) return null;
                            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            gradient.addColorStop(0, 'rgba(37, 99, 235, 0)');
                            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.1)');
                            return gradient;
                        },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { family: 'Inter', size: 10, weight: 'bold' },
                            bodyFont: { family: 'Inter', size: 12, weight: 'black' },
                            padding: 10,
                            displayColors: false,
                            cornerRadius: 12
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { 
                                stepSize: 1,
                                font: { size: 10, weight: 'bold' },
                                color: '#94a3b8'
                            },
                            grid: { display: false }
                        },
                        x: {
                            ticks: { 
                                font: { size: 10, weight: 'bold' },
                                color: '#94a3b8'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    </script>
    @endscript
</div>
