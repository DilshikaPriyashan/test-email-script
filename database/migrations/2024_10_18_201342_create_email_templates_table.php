<?php

use App\Enums\EmailAuthMethods;
use App\Models\Team;
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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('api_ref');
            $table->json('attributes')->default('[]');
            $table->longText('content')->default('');
            $table->enum('auth_mechanism', array_column(EmailAuthMethods::cases(), 'value'))->default(EmailAuthMethods::NONE->value);
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
