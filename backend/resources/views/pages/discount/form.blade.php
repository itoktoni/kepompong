<?php /** @var App\Models\Discount $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="discount_code" />
                <x-input col="4" name="discount_nama" />
                <x-select col="4" name="discount_type" :options="['percentage' => 'Percentage', 'fixed' => 'Fixed']" />
                <x-input col="4" name="discount_value" />
                <x-input col="4" name="discount_min_transaction" />
                <x-input col="4" name="discount_max_amount" />
                <x-toggle col="4" name="discount_active" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
