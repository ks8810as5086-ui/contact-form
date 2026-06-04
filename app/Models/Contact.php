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
}
