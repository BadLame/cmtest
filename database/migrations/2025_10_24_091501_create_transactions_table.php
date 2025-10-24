<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')
                ->references('user_id')->on('users_balances')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->float('amount', 2);
            $table->string('type');
            $table->timestamps();
        });
    }

    function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
