<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // ユーザー名更新
        $user->name = $validated['name'];
        $user->save();

        $profileData = $validated;

        // 画像アップロード処理
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $profileData['profile_image'] = $path;
        } else {
            unset($profileData['profile_image']);
        }

        // profile テーブルを更新または作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // ✅ マイリストタブを有効にして商品一覧へリダイレクト
        return redirect()
            ->route('mypage.index')
            ->with('success', 'プロフィールを更新しました。');
    }
}
