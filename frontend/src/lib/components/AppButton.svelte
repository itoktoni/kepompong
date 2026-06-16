<script>
  let {
    type = 'button',
    variant = 'primary',
    disabled = false,
    loading = false,
    onclick,
    children
  } = $props()

  const variants = {
    primary: 'btn-primary',
    secondary: 'btn-secondary',
    outline: 'btn-outline',
    danger: 'btn-danger',
    ghost: 'btn-ghost',
  }
</script>

<button
  {type}
  {disabled}
  class="{variants[variant] || variants.primary} {loading ? 'opacity-70 pointer-events-none' : ''}"
  onclick={onclick}>
  {#if loading}
    <span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin mr-2"></span>
  {/if}
  {@render children?.()}
</button>
