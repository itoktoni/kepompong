<?php /** @var App\Models\Payment $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="payment_id_user" />
                <x-input col="4" name="payment_id_plan" />
                <x-input col="4" name="payment_order_code" />
                <x-input col="4" name="payment_jumlah" />
                <x-input col="4" name="payment_total" />
                <x-input col="4" name="payment_status" />
                <x-input col="4" name="payment_metode" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
