<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            // $table->foreignIdFor(User::class, 'user_id')->constrained();
            // $table->foreignIdFor(User::class, 'followed_id')->constrained();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('followed_id')->constrained('users');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
