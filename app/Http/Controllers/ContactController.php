<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    // お問い合わせ一覧表示
    public function index()
    {
        // 全カテゴリ、タグを取得してビューへ渡す
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    // お問い合わせ内容確認
    public function confirm(StoreContactRequest $request)
    {
        // バリデーションはStoreContactRequestで行う
        $validated = $request->validated();

        // カテゴリ名を取得する
        $category = Category::find($validated['category_id']);
        // タグ名を取得する
        $tags = ! empty($validated['tag_ids']) ? Tag::whereIn('id', $validated['tag_ids'])->get() : collect();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    // お問い合わせ内容保存
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        // トランザクションを用いて保存と紐づけを行う
        DB::transaction(function () use ($validated, $request) {
            $contact = Contact::create($validated);

            // タグの紐づけ
            if ($request->filled('tag_ids')) {
                $contact->tags()->sync($validated['tag_ids']);
            }
        });

        return redirect()->route('contact.thanks');
    }
}
