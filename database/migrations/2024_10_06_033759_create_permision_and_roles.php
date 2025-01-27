<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(
            ['name' => Roles::Admin->value]
        );
        Role::create(
            ['name' => Roles::Client->value]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::query()->
        where('name', Roles::Admin->value)
            ->orWhere('name', Roles::Client->value)->delete();
    }
};
