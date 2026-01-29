<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin người dùng')
                    ->description('Nhập thông tin tài khoản người dùng')
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required(),
                                DateTimePicker::make('email_verified_at')
                                    ->label('Email xác nhận lúc'),
                                TextInput::make('password')
                                    ->label('Mật khẩu')
                                    ->password()
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
