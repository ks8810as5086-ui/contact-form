<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    // お問い合わせは1つのカテゴリに属する
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // お問い合わせは複数のタグを持つ
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeSearch($query, $params)
    {
        // 名前、メールの検索（keyword）
        if (! empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->where('first_name', 'like', '%'.$params['keyword'].'%')
                    ->orWhere('last_name', 'like', '%'.$params['keyword'].'%')
                    ->orWhere('email', 'like', '%'.$params['keyword'].'%');
            });
        }

        // 性別の検索（gender）※0は未選択とみなす
        if (! empty($params['gender']) && $params['gender'] != '0') {
            $query->where('gender', $params['gender']);
        }

        // カテゴリの検索（category_id）
        if (! empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        // 日付の検索（date）
        if (! empty($params['date'])) {
            $query->whereDate('created_at', $params['date']);
        }
    }
}
