-- ============================================
-- 現在のDBを多対多関係に変更するためのSQL文
-- 実行前に本番データベースのバックアップを取ってください
-- ============================================

-- 現状確認：
-- ✅ member_team テーブルは既に存在
-- ✅ データも移行済み
-- ❌ members テーブルのteam_id, roleカラムがまだ存在

-- 1. 外部キー制約を削除（membersテーブルのteam_id関連）
ALTER TABLE `members` DROP FOREIGN KEY `members_team_id_foreign`;

-- 2. インデックスを削除
ALTER TABLE `members` DROP INDEX `members_team_id_foreign`;

-- 3. 不要なカラムを削除
ALTER TABLE `members` DROP COLUMN `team_id`;
ALTER TABLE `members` DROP COLUMN `role`;

-- 4. 外部キー制約を追加（member_teamテーブル用 - 既に存在している可能性があるため確認）
-- 注意: 既に存在する場合はエラーになるため、必要に応じてコメントアウト
-- ALTER TABLE `member_team` 
--   ADD CONSTRAINT `member_team_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `member_team_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

-- ============================================
-- 確認用クエリ（実行後に動作確認）
-- ============================================

-- membersテーブルの構造確認
DESCRIBE members;

-- member_teamテーブルの確認
SELECT COUNT(*) as total_member_team_records FROM member_team;

-- チームとメンバーの関係確認
SELECT 
    t.name as team_name,
    m.name as member_name,
    mt.role,
    mt.is_active
FROM teams t
JOIN member_team mt ON t.id = mt.team_id
JOIN members m ON m.id = mt.member_id
ORDER BY t.name, m.name;

-- 外部キー制約の確認
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'LAA0956269-taskapp' 
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
