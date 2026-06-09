<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageAccessTest extends TestCase
{
    use RefreshDatabase; // テスト実行前にDBをリセットする

    public function test_お問い合わせフォームが表示され変数が渡されている(): void
    {
        // ビューに渡されるデータを準備
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        // お問い合わせフォーム入力ページ('/')にGETリクエストを送る
        $response = $this->get('/');

        $response->assertStatus(200);

        // 上記変数が渡されているか
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        // ページ内にカテゴリ、タグ名が含まれているか確認
        $response->assertSee($category->name);
        $response->assertSee($tag->name);
    }

    public function test_サンクスページが表示される(): void
    {
        // '/thanks'にGETリクエストを送る
        $response = $this->get('/contacts/thanks');

        $response->assertStatus(200);
    }
}
