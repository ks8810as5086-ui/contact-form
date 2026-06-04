<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    // カテゴリは複数のお問い合わせを持つ
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
