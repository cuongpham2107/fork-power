<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Battery;
use App\Models\BatteryUsage;
use App\Models\ForkLift;
use Illuminate\Http\Request;

class BatteryUsageController extends Controller
{
    /**
     * POST /api/battery-usages
     *
     * Luồng 1 - Lắp bình (không có hour_out):
     *   - Tìm battery theo code, forklift theo tên
     *   - Tạo bản ghi BatteryUsage mới, cập nhật battery → in_use
     *
     * Luồng 2 - Tháo bình (có hour_out):
     *   - Tìm battery theo code, forklift theo tên
     *   - Tìm bản ghi BatteryUsage đang running của cặp (battery, forklift)
     *   - Cập nhật hour_out, working_hours, status → finished
     *   - Cập nhật battery → standby, forklift → total_working_hours
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'battery_code' => 'required|string',
            'fork_lift_name' => 'required|string',
            'hour_initial' => 'required_without:hour_out|nullable|numeric|min:0',
            'hour_out' => 'nullable|numeric|min:0',
            'charger_bar' => 'required_without:hour_out|nullable|integer|min:1|max:4',
            'screen_bar' => 'required_without:hour_out|nullable|integer|min:1|max:10',
            'installed_by' => 'nullable|integer|exists:users,id',
            'installed_at' => 'nullable|date',
        ], [
            'battery_code.required' => 'Vui lòng cung cấp mã bình pin.',
            'fork_lift_name.required' => 'Vui lòng cung cấp tên xe nâng.',
            'hour_initial.required_without' => 'Vui lòng cung cấp số giờ vào.',
            'hour_initial.numeric' => 'Số giờ vào phải là số.',
            'hour_initial.min' => 'Số giờ vào không được nhỏ hơn 0.',
            'hour_out.numeric' => 'Số giờ ra phải là số.',
            'hour_out.min' => 'Số giờ ra không được nhỏ hơn 0.',
            'charger_bar.required_without' => 'Vui lòng cung cấp vạch pin nạp.',
            'charger_bar.min' => 'Vạch pin nạp tối thiểu là 1.',
            'charger_bar.max' => 'Vạch pin nạp tối đa là 4.',
            'screen_bar.required_without' => 'Vui lòng cung cấp vạch màn hình.',
            'screen_bar.min' => 'Vạch màn hình tối thiểu là 1.',
            'screen_bar.max' => 'Vạch màn hình tối đa là 10.',
        ]);

        // Tìm bình pin theo code
        $battery = Battery::where('code', $validated['battery_code'])->first();
        if (! $battery) {
            return response()->json(['message' => 'Bình pin không tồn tại.'], 422);
        }

        // Tìm xe nâng theo tên (like)
        $forklift = ForkLift::where('name', 'like', '%'.$validated['fork_lift_name'].'%')->first();
        if (! $forklift) {
            return response()->json(['message' => 'Xe nâng không tồn tại.'], 422);
        }
        
        // ── LUỒNG 2: Tháo bình (có hour_out) ────────────────────────────────
        if (! empty($validated['hour_out'])) {
            // Tìm bản ghi đang chạy của cặp (battery, forklift)
            $batteryUsage = BatteryUsage::where('battery_id', $battery->id)
                ->where('fork_lift_id', $forklift->id)
                ->where('status', 'running')
                ->first();

            if (! $batteryUsage) {
                return response()->json([
                    'message' => 'Không tìm thấy bản ghi đang chạy cho bình này trên xe nâng đã chỉ định.',
                ], 422);
            }

            // Số giờ ra phải >= số giờ vào
            if ((float) $validated['hour_out'] < (float) $batteryUsage->hour_initial) {
                return response()->json([
                    'message' => 'Số giờ ra phải lớn hơn hoặc bằng số giờ vào ('
                        .number_format($batteryUsage->hour_initial, 2).'h).',
                ], 422);
            }

            $hourOut = (float) $validated['hour_out'];
            $workingHours = $hourOut - (float) $batteryUsage->hour_initial;

            $batteryUsage->update([
                'hour_out' => $hourOut,
                'working_hours' => $workingHours,
                'removed_at' => now(),
                'removed_by' => 27,
                'status' => 'finished',
            ]);

            // Cập nhật trạng thái bình pin → standby
            $battery->update(['status' => 'standby']);

            // Cập nhật tổng giờ làm việc của xe nâng
            $forklift->update(['total_working_hours' => $hourOut]);

            return response()->json([
                'message' => 'Tháo bình thành công.',
                'data' => $batteryUsage->load(['battery', 'forkLift', 'installedBy']),
            ], 200);
        }

        // ── LUỒNG 1: Lắp bình (không có hour_out) ───────────────────────────

        // Bình pin phải ở trạng thái có thể lắp (không phải in_use)
        if ($battery->status === 'in_use') {
            return response()->json([
                'message' => 'Bình pin này đang được sử dụng, không thể lắp vào xe khác.',
            ], 422);
        }

        // Số giờ vào phải >= tổng giờ hiện tại của xe
        if ((float) $validated['hour_initial'] <= (float) $forklift->total_working_hours) {
            return response()->json([
                'message' => 'Số giờ vào phải lớn hơn hoặc bằng số giờ hiện tại của xe ('
                    .number_format($forklift->total_working_hours, 2).'h).',
            ], 422);
        }

        $batteryUsage = BatteryUsage::where('fork_lift_id', $forklift->id)
            ->where('status', 'running')
            ->first();
        // Kiểm tra 1 xe chỉ được lắp 1 bình
        if ($batteryUsage) {
            //Nếu xe nâng này đã được lắp bình, thì tháo bình ra
            $hourOut = (float) $validated['hour_initial'];
            $workingHours = $hourOut - (float) $batteryUsage->hour_initial;
            $batteryUsage->update([
                'hour_out' => $hourOut,
                'working_hours' => $workingHours,
                'status' => 'finished',
                'removed_at' => now(),
                'removed_by' => 27,
            ]);
            //update tổng giờ làm việc của xe nâng
            $forklift->update(['total_working_hours' => $batteryUsage->hour_out]);
            // update trạng thái cho bình cũ
            $batteryUsage->battery->update(['status' => 'standby']);
        }

        $batteryUsage = BatteryUsage::create([
            'battery_id' => $battery->id,
            'fork_lift_id' => $forklift->id,
            'charger_bar' => $validated['charger_bar'],
            'screen_bar' => $validated['screen_bar'],
            'hour_initial' => $validated['hour_initial'],
            'installed_at' => $validated['installed_at'] ?? now(),
            'installed_by' => 27,
            'status' => 'running',
        ]);

        // Cập nhật trạng thái bình pin → in_use
        $battery->update([
            'status' => 'in_use',
        ]);

        return response()->json([
            'message' => 'Lắp bình thành công.',
            'data' => $batteryUsage->load(['battery', 'forkLift', 'installedBy']),
        ], 201);
    }
}
