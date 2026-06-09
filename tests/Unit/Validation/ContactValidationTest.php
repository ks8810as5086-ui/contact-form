<?php

namespace Tests\Unit\Validation;

use App\Http\Requests\IndexContactRequest;
use App\Http\Requests\StoreContactRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ContactValidationTest extends TestCase
{
    public function test_問い合わせ検索のバリデーション(): void
    {
        $request = new IndexContactRequest;
        $rules = $request->rules();

        $validData = [
            'keyword' => 'テスト',
            'gender' => 1,
            'category_id' => 1,
            'date' => '2026-06-09',
        ];
        $validator = Validator::make($validData, $rules);
        $this->assertFalse($validator->fails(), '正常な検索パラメータは許可される');

        $invalidData = ['gender' => 'abc'];
        $validator = Validator::make($invalidData, ['gender' => $rules['gender']]);

        $this->assertTrue($validator->fails(), '不正な性別値（文字列）は拒否される');
    }

    public function test_お問い合わせ保存の必須項目と電話番号のバリデーション(): void
    {
        $rules = (new StoreContactRequest)->rules();

        // 1. タグ入力のテスト
        $validator = Validator::make(['tag_ids' => [1, 2]], ['tag_ids' => $rules['tag_ids']]);
        $this->assertFalse($validator->fails());

        // 2. 電話番号のテスト
        // OKなパターン
        $validator = Validator::make(['tel' => '09012345678'], ['tel' => $rules['tel']]);
        $this->assertFalse($validator->fails());

        // NGなパターン (ハイフン入り) ※要件の「不正な形式を拒否」を確認するため
        $validator = Validator::make(['tel' => '090-1234-5678'], ['tel' => $rules['tel']]);
        $this->assertTrue($validator->fails(), 'ハイフン入りは拒否されるべきです');

        // 3. 必須項目のテスト
        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->fails());
    }
}
