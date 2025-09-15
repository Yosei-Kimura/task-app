<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 既存のデータを中間テーブルに移行
        $members = DB::table('members')->get();
        
        foreach ($members as $member) {
            if ($member->team_id) {
                DB::table('member_team')->insert([
                    'member_id' => $member->id,
                    'team_id' => $member->team_id,
                    'role' => $member->role,
                    'is_active' => $member->is_active,
                    'created_at' => $member->created_at,
                    'updated_at' => $member->updated_at,
                ]);
            }
        }
        
        // membersテーブルからteam_idとroleを削除
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // membersテーブルにteam_idとroleを復元
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('role')->default('member');
        });
        
        // 中間テーブルのデータをmembersテーブルに戻す（最初の所属チームのみ）
        $memberTeams = DB::table('member_team')->get();
        
        foreach ($memberTeams as $memberTeam) {
            DB::table('members')
                ->where('id', $memberTeam->member_id)
                ->whereNull('team_id')
                ->update([
                    'team_id' => $memberTeam->team_id,
                    'role' => $memberTeam->role,
                ]);
        }
        
        // 中間テーブルを削除
        Schema::dropIfExists('member_team');
    }
};
