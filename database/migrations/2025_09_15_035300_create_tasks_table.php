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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('progress_status_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('due_date')->nullable(); // 期限
            $table->integer('priority')->default(1); // 優先度 1-5
            $table->boolean('is_reminder_sent')->default(false); // リマインド送信済みフラグ
            $table->datetime('reminder_sent_at')->nullable(); // リマインド送信時刻
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
