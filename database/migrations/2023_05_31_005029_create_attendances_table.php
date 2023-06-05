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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index('fk_attendances_to_users');
            $table->date('tanggal');
            $table->string('waktu_masuk');
            $table->string('waktu_pulang');
            $table->enum('status_masuk', ['APPROVE', 'REJECT'])->default('REJECT');
            $table->enum('status_pulang', ['APPROVE', 'REJECT'])->default('REJECT');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
