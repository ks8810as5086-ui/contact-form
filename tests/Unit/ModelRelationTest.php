<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationTest extends TestCase
{
    use RefreshDatabase;

    // 1. 1つのカテゴリから、紐づく複数のお問い合わせ（hasMany）が正しく取得できること
    public function test_カテゴリから複数の問い合わせが取得できる(): void
    {
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->contacts);
        $this->assertInstanceOf(Contact::class, $category->contacts->first());
    }

    // 2. 1つのお問い合わせが特定のカテゴリに属し、複数のタグと同期（sync）できること
    public function test_問い合わせはカテゴリに属しタグと同期できる(): void
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create(['category_id' => $category->id]);
        $tags = Tag::factory()->count(3)->create();

        // カテゴリの確認
        $this->assertEquals($category->id, $contact->category->id);

        // タグの同期確認
        $contact->tags()->sync($tags->pluck('id'));
        $this->assertCount(3, $contact->tags);
    }

    // 3. 中間テーブルを介して、1つのタグが複数のお問い合わせに紐づいていること
    public function test_1つのタグが複数の問い合わせに紐づく(): void
    {
        $tag = Tag::factory()->create();
        $category = Category::factory()->create();
        $contacts = Contact::factory()->count(2)->create();

        // 中間テーブルにデータを追加
        $tag->contacts()->attach($contacts->pluck('id'));

        $this->assertCount(2, $tag->contacts);
    }
}
