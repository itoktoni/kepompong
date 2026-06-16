<?php /** @var App\Models\Cashout $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="cashout_id_user" />
                <x-input col="4" name="cashout_jumlah" />
                <x-input col="4" name="cashout_status" />
                <x-input col="4" name="cashout_rekening_bank" />
                <x-input col="4" name="cashout_rekening_nomor" />
                <x-input col="4" name="cashout_rekening_nama" />
                <x-textarea col="12" name="cashout_catatan" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
