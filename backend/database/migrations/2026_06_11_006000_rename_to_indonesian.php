<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate', function (Blueprint $table) {
            $table->renameColumn('affiliate_type', 'affiliate_tipe');
            $table->renameColumn('affiliate_amount', 'affiliate_jumlah');
            $table->renameColumn('affiliate_payment_amount', 'affiliate_payment_jumlah');
            $table->renameColumn('affiliate_note', 'affiliate_catatan');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_amount', 'payment_jumlah');
            $table->renameColumn('payment_discount', 'payment_diskon');
            $table->renameColumn('payment_method', 'payment_metode');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate', function (Blueprint $table) {
            $table->renameColumn('affiliate_tipe', 'affiliate_type');
            $table->renameColumn('affiliate_jumlah', 'affiliate_amount');
            $table->renameColumn('affiliate_payment_jumlah', 'affiliate_payment_amount');
            $table->renameColumn('affiliate_catatan', 'affiliate_note');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_jumlah', 'payment_amount');
            $table->renameColumn('payment_diskon', 'payment_discount');
            $table->renameColumn('payment_metode', 'payment_method');
        });
    }
};
