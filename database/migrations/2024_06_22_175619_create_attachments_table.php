<?php

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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attachmentable_id');
            $table->string('attachmentable_type', 55);
            $table->string('name', 255);
            $table->string('path', 255);
            $table->string('mime', 25);
            // $table->integer('size')->nullable();
            $table->integer('size');
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
        Schema::dropIfExists('attachments');
    }
};
