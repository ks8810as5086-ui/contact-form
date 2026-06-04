<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // タグは複数のお問い合わせを持つ
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
