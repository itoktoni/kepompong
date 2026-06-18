<script>
  import { slide } from 'svelte/transition'

  let { anakList = [], value = null, onselect = () => {} } = $props()

  const selected = $derived(anakList.find(a => a.id === value))
  let open = $state(false)

  function handleSelect(id) {
    onselect(id)
    open = false
  }

  function handleClickOutside(e) {
    if (!e.target.closest('.anak-dropdown')) {
      open = false
    }
  }

  $effect(() => {
    if (open) {
      document.addEventListener('click', handleClickOutside)
      return () => document.removeEventListener('click', handleClickOutside)
    }
  })
</script>

{#if anakList.length > 0}
  <div class="relative inline-block anak-dropdown">
    <button
      class="flex items-center gap-2.5 bg-white rounded-2xl pl-4 pr-5 py-2.5 soft-shadow border-2 border-outline-variant hover:border-primary transition-colors cursor-pointer min-w-[160px]"
      onclick={() => open = !open}
    >
      <span class="w-8 h-8 rounded-full flex items-center justify-center text-base shrink-0"
        style="background: {selected?.bg || '#E3F2FD'}">{selected?.emoji || '👶'}</span>
      <span class="flex-1 text-left text-sm font-bold text-text-main truncate">{selected?.nama || 'Pilih Anak'}</span>
      <span class="text-on-surface-variant text-lg transition-transform duration-200"
        class:rotate-180={open}>▾</span>
    </button>

    {#if open}
      <div class="absolute right-0 top-full mt-2 bg-white rounded-2xl shadow-xl border border-outline-variant overflow-hidden z-50 min-w-[180px]"
        transition:slide={{ duration: 150 }}>
        {#each anakList as anak (anak.id)}
          <button
            class="flex items-center gap-3 w-full px-4 py-2.5 cursor-pointer transition-colors hover:bg-surface-container-low text-left"
            class:bg-success-soft={value === anak.id}
            onclick={() => handleSelect(anak.id)}
          >
            <span class="w-8 h-8 rounded-full flex items-center justify-center text-base shrink-0"
              style="background: {anak.bg || '#E3F2FD'}">{anak.emoji || '👶'}</span>
            <span class="text-sm font-medium text-text-main">{anak.nama}</span>
            {#if value === anak.id}
              <span class="text-primary text-lg ml-auto">✓</span>
            {/if}
          </button>
        {/each}
      </div>
    {/if}
  </div>
{/if}
