<?php

use App\Models\MyCourse;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->foreignIdFor(MyCourse::class, 'my_course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('price');
            $table->text('midtrans_snap_url');
            $table->enum('status', ['Pending', 'Success', 'Failed'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
