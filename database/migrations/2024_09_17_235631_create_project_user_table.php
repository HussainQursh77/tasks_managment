<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('user_role', ['developer', 'tester', 'manager']);
            $table->decimal('contribution_hours', 8, 2)->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('start_time')->nullable(); // Track when work starts
            $table->timestamp('end_time')->nullable();   // Track when work ends
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
