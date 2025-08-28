<?php

use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('sort')->default(0);
            $table->string('description')->nullable();
            $table->foreignId('producer_id')->constrained('users');
            $table->foreignId('executor_id')->constrained('users');
            $table->string('status')->default(TaskStatus::NEW);
            $table->date('due_date');
            $table->date('actual_date_of_execution')->nullable();
            $table->boolean('in_archive')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
