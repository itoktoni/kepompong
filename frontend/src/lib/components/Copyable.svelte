<script>
  let { value = '', label = '', format = 'text', class: className = '' } = $props()
  let copied = $state(false)

  async function copy() {
    try {
      await navigator.clipboard.writeText(String(value))
      copied = true
      setTimeout(() => copied = false, 2000)
    } catch (e) {
      // Fallback for older browsers
      const textarea = document.createElement('textarea')
      textarea.value = String(value)
      document.body.appendChild(textarea)
      textarea.select()
      document.execCommand('copy')
      document.body.removeChild(textarea)
      copied = true
      setTimeout(() => copied = false, 2000)
    }
  }

  function formatValue(val) {
    if (format === 'rupiah') {
      return `Rp${Number(val).toLocaleString('id-ID')}`
    }
    if (format === 'date') {
      return new Date(val).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
    }
    return val
  }

  let displayValue = $derived(formatValue(value))
</script>

<div class="flex items-center gap-2 {className}">
  {#if label}
    <p class="text-xs text-on-surface-variant">{label}</p>
  {/if}
  <div class="flex items-center gap-2 flex-1">
    <p class="font-bold text-lg text-primary tracking-wide">
      {displayValue}
    </p>
    {#if copied}
      <span class="text-xs text-success font-medium">✓ Tersalin!</span>
    {/if}
  </div>
  <button
    onclick={copy}
    class="px-2 py-1 rounded-lg text-xs font-bold transition-all bg-success-soft text-primary border border-[#B7D9BC] hover:bg-primary hover:text-white"
  >
    {copied ? '✓' : 'Copy'}
  </button>
</div>
