<?php /** @var App\Models\Affiliate $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="affiliate_id_user" />
                <x-input col="4" name="affiliate_id_from_user" />
                <x-input col="4" name="affiliate_tipe" />
                <x-input col="4" name="affiliate_jumlah" />
                <x-input col="4" name="affiliate_commission_rate" />
                <x-input col="4" name="affiliate_status" />
                <x-textarea col="12" name="affiliate_catatan" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
