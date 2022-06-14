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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('tuskey', 255)->nullable()->default(null);
            $table->string('file_name', 255);
            $table->string('file_path', 255)->unique();
            $table->string('dir_path', 255);
            $table->bigInteger('size');
            $table->timestamps();

            // allow ownership
            $table->unsignedBigInteger('owner_id')->nullable()->default(NULL);
            $table->string('owner_type')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
