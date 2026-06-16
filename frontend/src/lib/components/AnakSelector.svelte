<script>
  let { anakList = [], value = $bindable(null) } = $props()
</script>

{#if anakList.length > 1}
  <div class="flex items-center gap-2 flex-wrap">
    <span class="text-sm font-semibold text-on-surface-variant">Pilih Anak:</span>
    {#each anakList as anak}
      <button
        class="px-3 py-1.5 rounded-full text-sm font-medium transition-all
          {value === anak.id ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'}"
        onclick={() => value = anak.id}>
        {anak.emoji || '👶'} {anak.nama}
      </button>
    {/each}
  </div>
{:else if anakList.length === 1}
  <div class="flex items-center gap-2">
    <span class="text-lg">{anakList[0].emoji || '👶'}</span>
    <span class="text-sm font-semibold text-text-main">{anakList[0].nama}</span>
  </div>
{/if}
