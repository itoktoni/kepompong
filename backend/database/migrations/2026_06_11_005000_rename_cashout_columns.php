<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashouts', function (Blueprint $table) {
            $table->renameColumn('id', 'cashout_id');
            $table->renameColumn('user_id', 'cashout_id_user');
            $table->renameColumn('amount', 'cashout_jumlah');
            $table->renameColumn('admin_fee', 'cashout_admin_fee');
            $table->renameColumn('received', 'cashout_diterima');
            $table->renameColumn('rekening_bank', 'cashout_rekening_bank');
            $table->renameColumn('rekening_nomor', 'cashout_rekening_nomor');
            $table->renameColumn('rekening_nama', 'cashout_rekening_nama');
            $table->renameColumn('status', 'cashout_status');
            $table->renameColumn('note', 'cashout_catatan');
        });

        Schema::table('cashouts', function (Blueprint $table) {
            $table->renameColumn('created_at', 'cashout_created_at');
            $table->renameColumn('updated_at', 'cashout_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('cashouts', function (Blueprint $table) {
            $table->renameColumn('cashout_id', 'id');
            $table->renameColumn('cashout_id_user', 'user_id');
            $table->renameColumn('cashout_jumlah', 'amount');
            $table->renameColumn('cashout_admin_fee', 'admin_fee');
            $table->renameColumn('cashout_diterima', 'received');
            $table->renameColumn('cashout_rekening_bank', 'rekening_bank');
            $table->renameColumn('cashout_rekening_nomor', 'rekening_nomor');
            $table->renameColumn('cashout_rekening_nama', 'rekening_nama');
            $table->renameColumn('cashout_status', 'status');
            $table->renameColumn('cashout_catatan', 'note');
            $table->renameColumn('cashout_created_at', 'created_at');
            $table->renameColumn('cashout_updated_at', 'updated_at');
        });
    }
};
