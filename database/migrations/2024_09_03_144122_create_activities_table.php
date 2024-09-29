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
        Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The user who performed the activity
                $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade'); // Nullable for users who are not members
                $table->string('browser');
                $table->timestamp('start_time');
                $table->timestamp('end_time')->nullable();
                $table->string('duration')->nullable(); // Duration in seconds
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
        Schema::dropIfExists('activities');
    }
};
