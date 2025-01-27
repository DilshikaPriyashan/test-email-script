<?php

use App\Models\Team;
use App\Models\User;
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
        Schema::create('team_user', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Team::class)->constrained()->cascadeOnDelete();
            $table->timestamp('invitation_accepted_at')->nullable();
            $table->longText('code')->nullable();
            $table->timestamp('last_invitation_send_at')->nullable();
            $table->foreignIdFor(User::class, 'invited_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
