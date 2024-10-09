<?php

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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'mentor_id');
            $table->string('title');
            $table->string('slug');
            $table->enum('category', ['Programming', 'Networking', 'UI/UX', 'Cyber Security', 'Digital Marketing', 'Multimedia']);
            $table->enum('type', ['Free', 'Premium']);
            $table->enum('level', ['All Level', 'Beginner', 'Intermediate', 'Advance']);
            $table->unsignedBigInteger('price');
            $table->longText('description');
            $table->boolean('is_draft')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
