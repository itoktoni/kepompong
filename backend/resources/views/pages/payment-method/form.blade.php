<?php /** @var App\Models\PaymentMethod $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                 
                <x-input col="6" name="payment_method_id" />
                <x-input col="6" name="payment_method_nama" />
                <x-input col="6" name="payment_method_person" />
                <x-input col="6" name="payment_method_rekening" />
                <x-input col="6" name="payment_method_transfer" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
