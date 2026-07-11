<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Guides
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('languages', 255)->nullable();
            $table->string('specializations', 255)->nullable();
            $table->integer('experience')->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->string('certification')->nullable();
            $table->string('verification_status')->default('Pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('guide_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->string('day_of_week', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Hotels
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_url')->nullable();
            $table->json('gallery')->nullable();
            $table->string('website')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->integer('stars')->default(3);
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active');
            $table->string('amenities')->nullable();
            $table->string('check_in_time', 20)->nullable();
            $table->string('check_out_time', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('room_type', 100);
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->integer('capacity')->default(2);
            $table->integer('available_count')->default(1);
            $table->string('image_url')->nullable();
            $table->string('amenities')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Restaurants
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_url')->nullable();
            $table->json('gallery')->nullable();
            $table->string('website')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('cuisine_type', 100)->nullable();
            $table->string('opening_hours', 120)->nullable();
            $table->decimal('avg_price', 10, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 80)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_menus');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('guide_availabilities');
        Schema::dropIfExists('guides');
    }
};
