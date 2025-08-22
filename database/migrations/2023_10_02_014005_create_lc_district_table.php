<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLcDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lc_district', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('name', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('slug', 50)->nullable();
            $table->string('name_with_type', 50)->nullable();
            $table->string('path', 50)->nullable();
            $table->string('path_with_type', 100)->nullable();
            $table->string('parent_code', 50)->nullable();
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')
                ->on('lc_city')->onDelete('cascade');
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
        Schema::dropIfExists('lc_district');
    }
}
