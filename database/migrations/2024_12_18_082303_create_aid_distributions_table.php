<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aid_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_id')->constrained('disaster_locations')->onDelete('cascade');
            $table->foreignId('shelter_id')->constrained('shelter_locations')->onDelete('cascade');
            $table->foreignId('aid_type_id')->constrained('aid_types')->onDelete('cascade');
            $table->integer('quantity');
            $table->dateTime('date');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aid_distributions');
    }
};
