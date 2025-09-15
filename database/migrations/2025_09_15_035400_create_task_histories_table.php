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
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('changed_by_member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->string('action'); // created, updated, deleted, status_changed, assigned
            $table->json('old_values')->nullable(); // 変更前の値
            $table->json('new_values')->nullable(); // 変更後の値
            $table->text('comment')->nullable(); // 変更コメント
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_histories');
    }
};
