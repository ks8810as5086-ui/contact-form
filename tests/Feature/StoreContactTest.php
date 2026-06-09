<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせフォーム確認画面と保存処理のテスト(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $data = [
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'first_name' => 'テスト',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'building' => 'テストビル',
            'detail' => 'テストのお問い合わせ内容です。',
        ];

        $response = $this->post('/contacts/confirm', $data);
        $response->assertStatus(200);
        $response->assertSee('テスト');

        $response = $this->post('/contacts', $data);

        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);

        $contact = Contact::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);

        $response->assertRedirect('/contacts/thanks');
    }

    public function test_バリデーションエラー時にリダイレクトされる(): void
    {
        $response = $this->post('/contacts', []);

        $response->assertSessionHasErrors([
            'category_id', 'first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'detail',
        ]);
    }
}
