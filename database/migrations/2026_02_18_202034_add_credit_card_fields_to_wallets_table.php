<?php

use App\Enums\WalletTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->enum('type', WalletTypes::cases())->default(WalletTypes::CHECKING->value)->after('name');
            $table->bigInteger('credit_limit')->nullable()->after('balance');
            $table->unsignedTinyInteger('closing_day')->nullable()->after('credit_limit');
            $table->unsignedTinyInteger('due_day')->nullable()->after('closing_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['type', 'credit_limit', 'closing_day', 'due_day']);
        });
    }
};
