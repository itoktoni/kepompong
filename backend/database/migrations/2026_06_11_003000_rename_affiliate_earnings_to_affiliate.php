<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate', function (Blueprint $table) {
            $table->id('affiliate_id');
            $table->integer('affiliate_id_user');
            $table->integer('affiliate_id_from_user');
            $table->integer('affiliate_id_payment')->nullable();
            $table->enum('affiliate_type', ['register', 'upgrade']);
            $table->integer('affiliate_amount');
            $table->integer('affiliate_payment_amount')->nullable();
            $table->integer('affiliate_commission_rate')->nullable();
            $table->text('affiliate_note')->nullable();
            $table->enum('affiliate_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->dateTime('affiliate_created_at')->nullable();
            $table->dateTime('affiliate_updated_at')->nullable();
        });

        // Migrate data from old table
        if (Schema::hasTable('affiliate_earnings')) {
            $rows = DB::table('affiliate_earnings')->get();
            foreach ($rows as $row) {
                DB::table('affiliate')->insert([
                    'affiliate_id' => $row->id,
                    'affiliate_id_user' => $row->user_id,
                    'affiliate_id_from_user' => $row->from_user_id,
                    'affiliate_id_payment' => $row->payment_id,
                    'affiliate_type' => $row->type,
                    'affiliate_amount' => $row->amount,
                    'affiliate_payment_amount' => $row->payment_amount,
                    'affiliate_commission_rate' => $row->commission_rate,
                    'affiliate_note' => $row->note,
                    'affiliate_status' => $row->status,
                    'affiliate_created_at' => $row->earning_created_at,
                    'affiliate_updated_at' => $row->earning_updated_at,
                ]);
            }
            Schema::dropIfExists('affiliate_earnings');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate');
    }
};
