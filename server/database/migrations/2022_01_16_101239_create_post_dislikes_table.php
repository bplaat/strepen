<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostDislikesTable extends Migration
{
    public function up(): void
    {
        Schema::create('post_dislikes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('post_id')
                ->references('id')
                ->on('posts');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->unique(['post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_dislikes');
    }
}
