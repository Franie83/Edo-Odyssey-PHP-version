<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 80)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 60);
            $table->unsignedBigInteger('entity_id');
            $table->string('code', 100)->unique();
            $table->string('image_path')->nullable();
            $table->integer('scan_count')->default(0);
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id']);
        });

        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label', 120)->nullable();
            $table->string('type', 30)->default('text');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->text('message');
            $table->string('type', 40)->default('info');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category', 80)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->string('link')->nullable();
            $table->string('position', 60)->default('sidebar');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('NGN');
            $table->string('status', 30)->default('pending');
            $table->string('provider', 40)->nullable();
            $table->string('reference', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('cms_settings');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('audit_logs');
    }
};
