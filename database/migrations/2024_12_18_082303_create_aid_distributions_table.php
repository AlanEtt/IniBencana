<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aid_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_location_id')->constrained()->onDelete('cascade');
            $table->foreignId('shelter_location_id')->constrained()->onDelete('cascade');
            $table->string('aid_type');
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aid_distributions');
    }
};
