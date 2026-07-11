<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('booking_type', 30);   // Guide | Hotel | Restaurant | Attraction | Event
            $table->unsignedBigInteger('target_id'); // ID of guide/hotel/restaurant etc.
            $table->string('target_name', 180)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('guests')->default(1);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('booking_status')->default('Pending'); // Pending|Approved|Confirmed|Completed|Cancelled|Rejected
            $table->text('special_requests')->nullable();
            $table->text('admin_comment')->nullable();
            $table->text('target_comment')->nullable();
            $table->boolean('points_awarded')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('target_type', 30);    // Attraction|Guide|Hotel|Restaurant
            $table->unsignedBigInteger('target_id');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->string('title', 160)->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'target_type', 'target_id']); // one review per entity per user
        });

        Schema::create('favourites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('target_type', 30);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();

            $table->unique(['user_id', 'target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourites');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bookings');
    }
};
