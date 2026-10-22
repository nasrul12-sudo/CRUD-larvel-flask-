-- ============================================================
-- Auto-executed saat container MySQL pertama kali dibuat
-- ============================================================
-- Tidak perlu CREATE TABLE di sini — Laravel migration yang handle.
-- File ini hanya untuk setup awal yang perlu di luar migration.

SET GLOBAL time_zone = '+07:00';

-- Pastikan user aplikasi punya privilege minimum (sudah dibuat otomatis
-- oleh image MySQL via MYSQL_USER, jadi ini hanya verifikasi)
-- GRANT ALL PRIVILEGES ON database_erp.* TO 'erp_user'@'%';
-- FLUSH PRIVILEGES;
