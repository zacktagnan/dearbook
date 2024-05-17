<?php

use App\Models\Group;
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
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
            $table->string('status', 25);
            $table->string('role', 25);
            $table->string('token', 1024)->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('token_uses_at')->nullable();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Group::class)->constrained();
            // $table->foreignIdFor(User::class, 'created_by')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
