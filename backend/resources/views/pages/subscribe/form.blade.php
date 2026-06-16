<?php /** @var App\Models\Subscribe $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                 
                <x-input col="6" name="subscribe_id" />
                <x-input col="6" name="subscribe_id_user" />
                <x-input col="6" name="subscribe_harga" />
                <x-input col="6" name="subscribe_discount" />
                <x-input col="6" name="subscribe_total" />
                <x-input col="6" name="subscribe_id_plan" />
                <x-input col="6" name="subscribe_trial_at" />
                <x-input col="6" name="subscribe_start_at" />
                <x-input col="6" name="subscribe_end_at" />
                <x-input col="6" name="subscribe_canceled_at" />
                <x-input col="6" name="subscribe_created_at" />
                <x-input col="6" name="subscribe_updated_at" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
