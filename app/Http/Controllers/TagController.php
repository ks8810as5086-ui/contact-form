<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    // タグの保存
    public function store(StoreTagRequest $request)
    {
        // バリデーション済みデータを受け取る
        Tag::create($request->validated());

        // 保存後、管理画面一覧へ戻る
        return redirect('/admin')->with('message', 'タグを追加しました');
    }

    // タグの編集
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    // タグの更新
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect('/admin')->with('message', 'タグを更新しました');
    }

    // タグの削除
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect('/admin')->with('message', 'タグを削除しました');
    }
}
