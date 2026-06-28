<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('color');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('pos_x', 5, 2);
            $table->decimal('pos_y', 5, 2);
            $table->decimal('width', 5, 2);
            $table->decimal('height', 5, 2);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('access_type', ['admin', 'staff'])->default('staff');
            $table->rememberToken(); // ← add this
            $table->timestamps();
        });

        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [
                'desktop',
                'printer',
                'photocopier',
                'telephone',
                'aircon',
                'appliance',
                'network',
                'monitor',
                'other'
            ]);
            $table->string('model_num')->nullable();
            $table->text('specs')->nullable();
            $table->boolean('sub_parts')->default(false);
            $table->foreignId('status_id')->constrained('device_statuses');
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->decimal('pos_x', 5, 2)->nullable();
            $table->decimal('pos_y', 5, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('device_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices');
            $table->string('name');
            $table->string('model_num')->nullable();
            $table->string('specs')->nullable();
            $table->foreignId('status_id')->constrained('device_statuses');
            $table->timestamps();
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->constrained('users');
            $table->date('date');
            $table->text('description');
            $table->foreignId('status_before_id')->constrained('device_statuses');
            $table->foreignId('status_after_id')->constrained('device_statuses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('device_parts');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('users');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('device_statuses');
    }
};
