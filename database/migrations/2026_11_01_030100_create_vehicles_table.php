<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 7)->unique();
            $table->string('fipe_code', 10);
            $table->string('year_id', 10);
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('model_year');
            $table->string('fuel');
            $table->decimal('fipe_price', 12, 2);
            $table->decimal('daily_rate', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
