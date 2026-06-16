<?php /** @var App\Models\Idea $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                 
                <x-input col="6" name="idea_id" />
                <x-input col="6" name="idea_title" />
                <x-input col="6" name="idea_description" />
                <x-input col="6" name="idea_moral" />
                <x-input col="6" name="idea_type" />
                <x-input col="6" name="idea_date" />
                <x-input col="6" name="idea_ai" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
