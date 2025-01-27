<?php

use App\Models\APIKey;
use App\Models\EmailTemplate;
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
        Schema::create('email_histories', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->nullable();
            $table->foreignIdFor(EmailTemplate::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(APIKey::class, 'sended_via')->nullable();
            $table->string('status')->default('waiting');
            $table->json('to')->default('[]');
            $table->json('cc')->default('[]');
            $table->json('bcc')->default('[]');
            $table->json('history')->default('[]');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_histories');
    }
};
