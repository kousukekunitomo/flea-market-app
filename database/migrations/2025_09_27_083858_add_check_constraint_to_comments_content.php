<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void {
    try { DB::statement("ALTER TABLE `comments` DROP CHECK `chk_comments_content_len`"); } catch (\Throwable $e) {}
    DB::statement("ALTER TABLE `comments`
      ADD CONSTRAINT `chk_comments_content_len`
      CHECK (CHAR_LENGTH(`content`) BETWEEN 1 AND 255)");
  }
  public function down(): void {
    try { DB::statement("ALTER TABLE `comments` DROP CHECK `chk_comments_content_len`"); } catch (\Throwable $e) {}
  }
};
};
