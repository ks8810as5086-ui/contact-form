<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 登録ページ一覧を表示する
    public function index()
    {
        // カテゴリを全件取得する
        $categories = Category::all();
        // お問い合わせ一覧を7件ごとにページネーションつきで取得
        $contacts = Contact::latest()->paginate(7);

        // ビューにデータを渡す
        return view('admin.index', compact('categories', 'contacts'));
    }
    // 登録ページの詳細を表示する
    // public function show(Request $request)
}
