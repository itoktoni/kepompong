<?php /** @var App\Models\Addon $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => 'Addon'], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="addon_id_user" />
                <x-input col="8" name="addon_nama" />
                <x-textarea col="12" name="addon_desc" />
                <x-input col="4" name="addon_harga" />
                <x-input col="4" name="addon_age" />
                <x-input col="4" name="addon_age_label" />
                <x-input col="4" name="addon_bg" />
                <x-input col="4" name="addon_icon" />
            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
