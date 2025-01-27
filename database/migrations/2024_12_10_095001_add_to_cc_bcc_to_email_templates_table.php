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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->json('to')->default('[]');
            $table->json('cc')->default('[]');
            $table->json('bcc')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('to');
            $table->dropColumn('cc');
            $table->dropColumn('bcc');
        });
    }
};
