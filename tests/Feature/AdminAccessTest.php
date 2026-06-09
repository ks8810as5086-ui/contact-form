<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_未ログインユーザーは管理画面にアクセスできない(): void
    {
        // ログインしないで/adminにアクセスする
        $response = $this->get('/admin');
        //　/loginにリダイレクトされることを確認
        $response->assertRedirect('/login');
    }

    public function test_ログイン済みユーザーは管理画面にアクセスできる(): void
    {
        // ユーザーを作成し、ログインした状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        //　管理画面/adminにアクセス
        $response = $this->get('/admin');

        $response->assertStatus(200);
    }
}
