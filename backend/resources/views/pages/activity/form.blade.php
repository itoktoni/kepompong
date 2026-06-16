<?php /** @var App\Models\Activity $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(module())], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="ucfirst(module())">
            @bind($model ?? null)
                <x-input col="4" name="title" />
                <x-input col="4" name="type" />
                <x-input col="4" name="slug" />
                <x-textarea col="12" name="desc" />
                <x-input col="12" name="moral" />
                <x-textarea col="12" :rows="10" name="prompt" />
                @if(isset($model) && $model->exists && $model->prompt)
                    <div class="col-span-12" x-data="{ copied: false }">
                        <button type="button" @click="navigator.clipboard.writeText(document.querySelector('[name=prompt]').value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center gap-2 h-9 px-4 text-sm font-semibold rounded-lg bg-primary text-on-primary hover:bg-primary/90 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-lg" x-text="copied ? 'check' : 'content_copy'"></span>
                            <span x-text="copied ? 'Copied!' : 'Copy Prompt'"></span>
                        </button>
                    </div>
                @endif
                <x-input col="2" name="pages" type="number" value="16" label="Pages (split)" />
                <x-input col="2" name="sort_order" />
                <x-toggle col="2" name="active" />
                <x-file col="3" name="file" accept="image/*" />


            @endbind

            @if(isset($model) && $model->exists && $model->image)
                <div class="col-span-12 md:col-span-3">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Current Image</label>
                    <img src="{{ \Illuminate\Support\Facades\Storage::url('images/stories/' . $model->slug . '/' . $model->image) }}" alt="Current" class="object-cover rounded-xl border-2 border-outline-variant">
                </div>
            @endif
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
