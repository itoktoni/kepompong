<script>
  let {
    type = 'text',
    value = $bindable(''),
    placeholder = '',
    label = '',
    error = '',
    disabled = false,
    required = false,
    id = '',
    oninput,
    passwordToggle = false,
  } = $props()

  let showPassword = $state(false)
  let inputType = $derived(passwordToggle && type === 'password' ? (showPassword ? 'text' : 'password') : type)
</script>

<div class="mb-4">
  {#if label}
    <label for={id} class="block text-sm font-semibold text-text-main mb-1.5">{label}</label>
  {/if}
  <div class="relative">
    <input
      type={inputType}
      {id}
      {placeholder}
      {disabled}
      {required}
      bind:value
      {oninput}
      class="w-full px-4 py-3 rounded-xl border {error ? 'border-error' : 'border-outline-variant'} bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors disabled:opacity-50 {passwordToggle ? 'pr-12' : ''}"
    />
    {#if passwordToggle && (type === 'password' || type === 'text')}
      <button
        type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-text-main transition-colors"
        onclick={() => { showPassword = !showPassword }}
        tabindex="-1"
      >
        <span class="material-symbols-outlined text-xl">{showPassword ? 'visibility_off' : 'visibility'}</span>
      </button>
    {/if}
  </div>
  {#if error}
    <p class="text-xs text-error mt-1">{error}</p>
  {/if}
</div>
