<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductCommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder を使ってユーザー・商品・カテゴリを準備
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * ログイン済みのユーザーはコメントを送信できる
     *
     * @return void
     */
    public function testLoggedInUserCanSendComment()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $commentContent = 'This is a sample comment.';

        $response = $this->post(route('comments.store', $product->id), [
            'content' => $commentContent
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'content' => $commentContent,
        ]);

        $response->assertRedirect(route('products.show', $product->id));
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     *
     * @return void
     */
    public function testNotLoggedInUserCannotSendComment()
    {
        $product = Product::first();

        $response = $this->post(route('comments.store', $product->id), [
            'content' => 'This is a sample comment.'
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function testCommentCannotBeEmpty()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $response = $this->post(route('comments.store', $product->id), [
            'content' => ''
        ]);

        $response->assertSessionHasErrors('content');
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function testCommentCannotExceed255Characters()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $response = $this->post(route('comments.store', $product->id), [
            'content' => str_repeat('a', 256)
        ]);

        $response->assertSessionHasErrors('content');
    }
}
