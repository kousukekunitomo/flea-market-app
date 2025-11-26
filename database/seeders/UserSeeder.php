<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ① Demo Login 用 管理者アカウント
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('mmmmmmmm'),
            ]
        );

        // ② テストユーザー（存在しないとエラーになる場合に備えて）
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // ③ ダミーユーザー user1〜user10（重複しないよう updateOrCreate に変更）
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "ユーザー{$i}",
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
