<?php /** @var App\Models\Plan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                 
                <x-input col="6" name="plan_id" />
                <x-input col="6" name="plan_nama" />
                <x-input col="6" name="plan_keteranan" />
                <x-input col="6" name="plan_harga" />
                <x-input col="6" name="plan_fee" />
                <x-input col="6" name="plan_periode" />
                <x-input col="6" name="plan_interval" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
