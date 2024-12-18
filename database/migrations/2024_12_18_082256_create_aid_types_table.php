<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aid_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('unit');
            $table->enum('priority_level', ['tinggi', 'sedang', 'rendah'])->default('sedang');
            $table->boolean('is_perishable')->default(false);
            $table->string('storage_method')->nullable();
            $table->string('distribution_method')->nullable();
            $table->string('donor_name');
            $table->string('donor_contact')->nullable();
            $table->enum('donor_type', ['individu', 'organisasi'])->default('individu');
            $table->timestamp('donation_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aid_types');
    }
};
