<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;

class AdminController extends Controller
{
    // 登録ページ一覧を表示する
    public function index(IndexContactRequest $request)
    {
        // カテゴリを全件取得する
        $categories = Category::all();
        // お問い合わせ一覧をバリデーション済みのデータを使って7件ごとにページネーションつきで取得
        $contacts = Contact::search($request->validated())->latest()->paginate(7);

        // ビューにデータを渡す
        return view('admin.index', compact('categories', 'contacts'));
    }

    // 登録ページの詳細を表示する
    public function show(Contact $contact)
    {
        return view('admin.show', compact('contact'));
    }

    // 登録ページを削除する
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.index')->with('success', 'お問い合わせを削除しました。');
    }
}
