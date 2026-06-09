<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みユーザーはタグを作成できる(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/admin/tags', ['name' => '新機能の要望']);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新機能の要望']);
    }

    public function test_未ログインユーザーはタグ作成ができない(): void
    {
        // ログインさせずにアクセス
        $response = $this->post('/admin/tags', ['name' => '不正なタグ']);

        // ログイン画面へリダイレクトされるか
        $response->assertRedirect('/login');
        // DBにデータが作成されていないか
        $this->assertDatabaseMissing('tags', ['name' => '不正なタグ']);
    }
}
