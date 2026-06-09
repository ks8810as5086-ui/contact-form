<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexContactRequest;
use App\Http\Requests\Api\StoreContactRequest;
use App\Http\Requests\Api\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{
    // 一覧取得
    public function index(IndexContactRequest $request): AnonymousResourceCollection
    {
        $query = Contact::query()->with(['category', 'tags']);

        // 検索条件の適用
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->keyword}%")
                    ->orWhere('last_name', 'like', "%{$request->keyword}%")
                    ->orWhere('email', 'like', "%{$request->keyword}%");
            });
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->paginate($request->input('per_page', 10));

        return ContactResource::collection($contacts);
    }

    // 詳細取得
    public function show(int $id): ContactResource
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        return new ContactResource($contact);
    }

    // 作成
    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        $contact->tags()->sync($request->tag_ids);

        return new ContactResource($contact->load(['category', 'tags']), 201);
    }

    // 更新
    public function update(UpdateContactRequest $request, int $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update($request->validated());
        $contact->tags()->sync($request->tag_ids);

        return new ContactResource($contact->fresh(['category', 'tags']));
    }

    // 削除
    public function destroy(int $id)
    {
        Contact::findOrFail($id)->delete();

        return response()->noContent();
    }

    // CSVエクスポート
    public function export(Request $request)
    {
        $query = Contact::query()->with(['category']);
        // 検索条件の継承
        if ($request->filled('keyword')) { /* ...省略(indexと同様の絞り込み) */
        }

        $contacts = $query->latest()->get();

        $callback = function () use ($contacts) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($file, ['ID', '氏名', '性別', 'メール', '電話', '住所', '建物', 'カテゴリ', '内容', '作成日時']);
            foreach ($contacts as $c) {
                fputcsv($file, [$c->id, $c->first_name.$c->last_name, $c->gender, $c->email, $c->tel, $c->address, $c->building, $c->category->content, $c->detail, $c->created_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="contacts.csv"']);
    }
}
