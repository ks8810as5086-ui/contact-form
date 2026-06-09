<?php

namespace Tests\Unit\Validation;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TagValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_タグ新規登録のバリデーション(): void
    {
        $rules = (new StoreTagRequest)->rules();

        $validator = Validator::make(['name' => ''], $rules);
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['name' => str_repeat('a', 51)], $rules);
        $this->assertTrue($validator->fails());

        Tag::factory()->create(['name' => '既存タグ']);
        $validator = Validator::make(['name' => '既存タグ'], $rules);
        $this->assertTrue($validator->fails(), '重複した名前は拒否される');
    }

    public function test_タグ更新のバリデーション(): void
    {
        $tag = Tag::factory()->create([
            'name' => '自身の名前',
        ]);

        Tag::factory()->create([
            'name' => '他人の名前',
        ]);

        $route = $this->createMock(Route::class);

        $route->method('parameter')
            ->with('tag')
            ->willReturn($tag->id);

        $request = new UpdateTagRequest;
        $request->setRouteResolver(fn () => $route);

        $rules = $request->rules();

        $validator = Validator::make([
            'name' => '自身の名前',
        ], $rules);

        $this->assertFalse($validator->fails());

        $validator = Validator::make([
            'name' => '他人の名前',
        ], $rules);

        $this->assertTrue($validator->fails());
    }
}
