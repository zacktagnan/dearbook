<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Posibles tipos de implementación de la columna...
            // $table->foreignId('pinned_post_id')->nullable()->after('user_id')->references('id')->on('posts');
            // $table->foreignId('pinned_post_id')->nullable()->after('user_id')->constrained('posts');
            $table->foreignIdFor(Post::class, 'pinned_post_id')->nullable()->after('user_id')->constrained('posts');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Post::class, 'pinned_post_id')->nullable()->after('avatar_path')->constrained('posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('pinned_post_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pinned_post_id');
        });
    }
};
