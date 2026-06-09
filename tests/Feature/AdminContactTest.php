<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_7件ごとのページネーションを確認(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $category = Category::factory()->create();

        // 境界値である8件のデータを作成
        Contact::factory()->count(8)->create(['category_id' => $category->id,
        ]);

        $response = $this->get('/admin?page=1');
        $response->assertStatus(200);

        $response = $this->get('/admin?page=2');
        $response->assertStatus(200);
    }

    public function test_管理画面でカテゴリフィルタが機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cat1 = Category::factory()->create([
            'content' => 'テストカテゴリ1',
        ]);

        $cat2 = Category::factory()->create([
            'content' => 'テストカテゴリ2',
        ]);

        Contact::factory()->create([
            'first_name' => '山田',
            'category_id' => $cat1->id,
        ]);

        Contact::factory()->create([
            'first_name' => '佐藤',
            'category_id' => $cat2->id,
        ]);

        $response = $this->get('/admin?category_id='.$cat1->id);

        $response->assertStatus(200);

        $response->assertSee('山田');
        $response->assertDontSee('佐藤');
    }

    public function test_管理画面でキーワード検索が機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Category::factory()->create();

        Contact::factory()->create(['first_name' => '山田', 'last_name' => '大作']);
        Contact::factory()->create(['first_name' => '佐藤', 'last_name' => '翔']);

        $response = $this->get('/admin?keyword=山田');

        $response->assertStatus(200);

        $response->assertSee('山田');

        $response->assertDontSee('佐藤');
    }

    public function test_管理画面で性別検索が機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        Contact::factory()->create([
            'first_name' => '田中',
            'gender' => 1,
            'category_id' => $category->id,
        ]);

        Contact::factory()->create([
            'first_name' => '鈴木',
            'gender' => 2,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/admin?gender=1');

        $response->assertStatus(200);

        $response->assertSee('田中');
        $response->assertDontSee('鈴木');
    }

    public function test_管理画面で日付検索が機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Category::factory()->create();

        Contact::factory()->create(['created_at' => '2026-01-01 10:00:00']);
        Contact::factory()->create(['created_at' => '2026-02-01 10:00:00']);

        $response = $this->get('/admin?from=2026-01-01&until=2026-01-31');

        $response->assertStatus(200);

        $response->assertSee('2026-01-01');

        $response->assertDontSee('2026-02-01');
    }

    public function test_管理画面で詳細ページが表示されカテゴリ名が含まれる(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create(['content' => '特定のカテゴリ名']);
        $contact = Contact::factory()->create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'email' => 'test@example.com',
        ]);

        $response = $this->get('/admin/contacts/'.$contact->id);

        $response->assertStatus(200);

        $response->assertSee('山田');
        $response->assertSee('test@example.com');

        $response->assertSee('特定のカテゴリ名');
    }

    public function test_管理画面で詳細ページから削除が機能する(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Category::factory()->create();

        $contact = Contact::factory()->create(['first_name' => '削除太郎']);

        $response = $this->delete('/admin/contacts/'.$contact->id);

        $response->assertRedirect('/admin');

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
            'first_name' => '削除太郎',
        ]);
    }
}
