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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('cover_path', 1024)->nullable();
            $table->string('thumbnail_path', 1024)->nullable();
            $table->boolean('auto_approval')->default(true);
            $table->text('about')->nullable();
            $table->foreignIdFor(User::class)->constrained();
            // $table->foreignIdFor(User::class, 'deleted_by')->nullable()->constrained();
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
