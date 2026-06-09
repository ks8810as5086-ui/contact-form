<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせ一覧_ap_iが正しく動作すること()
    {
        $category = Category::factory()->create();
        Contact::factory()->count(3)->create();
        $response = $this->getJson('/api/contacts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'first_name', 'last_name']]]);
    }

    public function test_お問い合わせ作成_ap_iがバリデーションエラーを返すこと()
    {
        $response = $this->postJson('/api/contacts', []);
        $response->assertStatus(422);
    }

    // 詳細APIのテスト
    public function test_お問い合わせ詳細_ap_i_異常系()
    {
        $this->getJson('/api/contacts/9999')->assertStatus(404);
    }

    // 削除APIのテスト
    public function test_お問い合わせ削除_api()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create();
        $this->deleteJson("/api/contacts/{$contact->id}")->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    // 更新APIのバリデーションテスト（422確認）
    public function test_お問い合わせ更新_ap_i_バリデーションエラー()
    {
        $category = Category::factory()->create();
        $contact = Contact::factory()->create();
        $this->putJson("/api/contacts/{$contact->id}", ['first_name' => '']) // 必須項目なし
            ->assertStatus(422);
    }
}
