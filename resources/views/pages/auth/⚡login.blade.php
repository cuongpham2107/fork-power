<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component
{
    public $username = '';
    public $password = '';
    public $remember = false;
    public $showPassword = false;

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = null;

        // 1. Attempt local login first
        if (Auth::attempt(['email' => $this->username, 'password' => $this->password], $this->remember) || 
            Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();
        } else {
            // 2. Fallback to ASGL API
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://id.asgl.net.vn/api/auth/login', [
                    'login' => $this->username,
                    'password' => $this->password,
                ]);

                if ($response->successful()) {
                    $userResponse = $response->json()['data']['user'];

                    // Find or create local user
                    $user = \App\Models\User::where('asgl_id', $userResponse['id'])
                        ->orWhere('username', $userResponse['username'])
                        ->first();

                    $userData = [
                        'name' => $userResponse['full_name'],
                        'username' => $userResponse['username'],
                        'mobile_phone' => $userResponse['mobile_phone'],
                        'asgl_id' => $userResponse['id'],
                        'avatar' => $userResponse['avatar'],
                        'department_name' => $userResponse['positions'][0]['department']['short_code'] ?? null,
                    ];

                    if ($user) {
                        $user->update($userData);
                    } else {
                        $user = \App\Models\User::create(array_merge($userData, [
                            'email' => $userResponse['email'] ?? ($userResponse['username'] . '@asgl.net.vn'),
                            'password' => Str::password(),
                        ]));
                    }
                }
            } catch (\Exception $e) {
                // Log the exception if needed
            }
        }

        if ($user) {
            Auth::login($user, $this->remember);
            session()->regenerate();
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'login_error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
        ]);
    }

    public function togglePassword()
    {
        $this->showPassword = ! $this->showPassword;
    }
};
?>

<div class="min-h-screen flex flex-col justify-center py-12 px-6 lg:px-8 bg-linear-to-br from-blue-50 to-white animate-page-entry">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="p-3 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-black text-gray-900 tracking-tight">Quản lý sạc pin</h2>
        <p class="mt-2 text-center text-sm text-gray-500 font-medium">Đăng nhập để quản lý hệ thống</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="py-8 px-3 rounded-3xl">
            @if ($errors->has('login_error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                    <div class="shrink-0 w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-red-500">
                            <path fill-rule="evenodd" d="M9.401 3.003c.115-.245.334-.614.59-.883.254-.269.601-.52 1.009-.52.408 0 .755.251 1.009.52.256.269.475.638.59.883l.005.01c.214.456.41 1.025.617 1.637.414 1.223.896 2.825 1.377 4.54l.001.002c.484 1.725.968 3.535 1.225 4.965.132.736.216 1.346.257 1.783.04.434.043.682.043.765 0 2.209-1.791 4-4 4s-4-1.791-4-4c0-.083.003-.331.043-.765.041-.437.125-1.047.257-1.783.257-1.43.741-3.24 1.225-4.965l.001-.002c.481-1.714.963-3.317 1.377-4.54.207-.612.403-1.181.617-1.637l.005-.01zM11 18a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-red-600 leading-tight">Đăng nhập thất bại</p>
                        <p class="text-xs font-medium text-red-500">{{ $errors->first('login_error') }}</p>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-bold text-gray-700">Tên đăng nhập</label>
                    <div class="mt-1.5 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input wire:model="username" id="username" type="text" placeholder="asgl-*****" required class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-2xl bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all sm:text-base">
                    </div>
                    @error('username') <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700">Mật khẩu</label>
                    <div class="mt-1.5 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input wire:model="password" id="password" type="{{ $showPassword ? 'text' : 'password' }}" placeholder="********" required class="block w-full pl-11 pr-12 py-3 border border-gray-200 rounded-2xl bg-gray-50/50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all sm:text-base">
                        
                        <button type="button" wire:click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                            @if($showPassword)
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 5c-1.84 0-3.35.39-4.62.97L3.7 2.29 2.29 3.7l3.32 3.32C3 8.97 2.07 11.64 2.05 11.68c-.07.21-.07.43 0 .63.02.07 2.32 6.68 9.95 6.68 1.84 0 3.35-.39 4.62-.97l3.68 3.68 1.41-1.41-3.32-3.32c2.61-1.95 3.54-4.62 3.56-4.66.07-.21.07-.43 0-.63C21.93 11.61 19.63 5 12 5m-7.93 7c.1-.24.27-.59.52-.99l5.87 5.87c-4.21-.65-5.94-3.84-6.39-4.88m9.25 4.91L5.84 9.43c.34-.34.74-.67 1.19-.98l8.05 8.05c-.53.19-1.12.33-1.76.41m3.65-1.35-1.53-1.53c.61-1.03.71-2.28.31-3.38-.18.21-.45.36-.75.36-.55 0-1-.45-1-1 0-.44.29-.81.69-.94a3.98 3.98 0 0 0-4.71-.5L8.92 7.51c.88-.31 1.9-.5 3.08-.5 5.35 0 7.42 3.85 7.93 5-.3.69-1.17 2.34-2.96 3.56Z"></path>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 9a3 3 0 1 0 0 6 3 3 0 1 0 0-6"></path>
                                    <path d="M12 19c7.63 0 9.93-6.62 9.95-6.68.07-.21.07-.43 0-.63-.02-.07-2.32-6.68-9.95-6.68s-9.93 6.61-9.95 6.67c-.07.21-.07.43 0 .63.02.07 2.32 6.68 9.95 6.68Zm0-12c5.35 0 7.42 3.85 7.93 5-.5 1.16-2.58 5-7.93 5s-7.42-3.84-7.93-5c.5-1.16 2.58-5 7.93-5"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input wire:model="remember" id="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-lg">
                        <label for="remember-me" class="ml-2.5 block text-sm font-medium text-gray-600">Ghi nhớ đăng nhập</label>
                    </div>
                </div>

                <div>
                    <button type="submit" wire:loading.attr="disabled" class="w-full flex items-center justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg shadow-blue-200 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="login">ĐĂNG NHẬP</span>
                        <span wire:loading.flex wire:target="login" class="items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-black text-white tracking-widest">ĐANG XỬ LÝ...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>