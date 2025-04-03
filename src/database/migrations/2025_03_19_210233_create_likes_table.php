<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            // 主キーを設定
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            // 複合主キーを設定
            $table->primary(['user_id', 'product_id']);

            // 外部キーを設定
            $table->foreign('user_id', 'likes_user_id_foreign') // 外部キー制約名を指定
                ->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id', 'likes_product_id_foreign') // 外部キー制約名を指定
                ->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
