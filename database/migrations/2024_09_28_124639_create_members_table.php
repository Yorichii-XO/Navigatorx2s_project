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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Nullable for cases where password might not be provided initially
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Assuming you have a 'roles' table
            $table->unsignedBigInteger('user_id')->nullable(); // Adjust as necessary
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null'); // Reference to inviter
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
        Schema::dropIfExists('members');
    }
};
