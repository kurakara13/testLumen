<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Checklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('checklists');
        Schema::create('checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_domain');
            $table->string('object_id');
            $table->string('description');
            $table->boolean('is_completed')->default(false);
            $table->string('completed_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('due')->nullable();
            $table->integer('urgency');
            $table->string('items');
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
        //
    }
}
