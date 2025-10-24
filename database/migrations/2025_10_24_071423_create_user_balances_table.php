<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void
    {
        Schema::create('users_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->float('balance', 2);
            $table->timestamps();
        });
    }

    function down(): void
    {
        Schema::dropIfExists('users_balances');
    }
};
