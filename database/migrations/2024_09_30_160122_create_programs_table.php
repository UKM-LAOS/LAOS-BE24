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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_title')->unique();
            $table->string('program_slug');
            $table->string('activity_title');
            $table->foreignId('division_id')->constrained();
            $table->longText('content');
            $table->double('latitude');
            $table->double('longitude');
            $table->date('open_registration');
            $table->date('close_registration');
            $table->text('embedded_gform');
            $table->json('program_schedules');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
