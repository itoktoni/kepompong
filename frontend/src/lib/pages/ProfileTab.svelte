<script>
  import { get } from 'svelte/store'
  import { anakList, addAnak, updateAnak, deleteAnak } from '../stores/anakStore.js'
  import { user, userPlan, userRole, serverDate, trialDays, logout as authLogout } from '../stores/authStore.js'
  import { switchTab, selectedAnakId, appReady } from '../stores/appStore.js'
  import { anakToolsData, toolsAnakId } from '../stores/toolsStore.js'
  import * as api from '../services/api.js'
  import AppButton from '../components/AppButton.svelte'
  import AppInput from '../components/AppInput.svelte'
  import AppModal from '../components/AppModal.svelte'

  let anakListVal = $state([])
  let userVal = $state(null)
  let userRoleVal = $state('')
  let userPlanVal = $state(null)
  let serverDateVal = $state(null)
  let trialDaysVal = $state(10)

  $effect(() => {
    const u1 = anakList.subscribe(v => anakListVal = v)
    const u2 = user.subscribe(v => userVal = v)
    const u3 = userRole.subscribe(v => userRoleVal = v)
    const u4 = userPlan.subscribe(v => userPlanVal = v)
    const u5 = serverDate.subscribe(v => serverDateVal = v)
    const u6 = trialDays.subscribe(v => trialDaysVal = v)
    return () => { u1(); u2(); u3(); u4(); u5(); u6() }
  })

  let maxChildren = $derived(
    userRoleVal === 'developer' ? Infinity :
    (userPlanVal?.plan_value || 1)
  )

  let canAddAnak = $derived.by(() => {
    if (userRoleVal === 'developer') return true
    if (!userPlanVal) return false
    if (userRoleVal === 'trial') {
      const trialStart = userPlanVal?.subscribe_trial_at
      if (!trialStart) return true
      const serverNow = serverDateVal ? new Date(serverDateVal) : new Date()
      const daysDiff = Math.floor((serverNow - new Date(trialStart)) / (1000 * 60 * 60 * 24))
      if (daysDiff > trialDaysVal) return false
    }
    return anakListVal.length < maxChildren
  })

  let showEditProfile = $state(false)
  let editName = $state('')
  let editPhone = $state('')
  let editGender = $state('')
  let editAgama = $state('')
  let profileSaving = $state(false)
  let profileError = $state('')

  const agamaOptions = [
    { value: 'islam', label: 'Islam' },
    { value: 'kristen_protestan', label: 'Kristen Protestan' },
    { value: 'kristen_katolik', label: 'Kristen Katolik' },
    { value: 'hindu', label: 'Hindu' },
    { value: 'buddha', label: 'Buddha' },
    { value: 'konghucu', label: 'Konghucu' },
  ]

  function openEditProfile() {
    editName = userVal?.name || ''
    editPhone = userVal?.phone || ''
    editGender = userVal?.gender || ''
    editAgama = userVal?.user_agama || ''
    profileError = ''
    showEditProfile = true
  }

  async function saveProfile() {
    if (!editName.trim()) { profileError = 'Nama wajib diisi'; return }
    if (!editPhone.trim()) { profileError = 'Nomor telepon wajib diisi'; return }
    profileSaving = true; profileError = ''
    try {
      await api.updateProfile({ name: editName.trim(), phone: editPhone.trim(), gender: editGender || '', user_agama: editAgama || null })
      user.update(u => u ? { ...u, name: editName.trim(), phone: editPhone.trim(), gender: editGender, user_agama: editAgama } : u)
      showEditProfile = false
    } catch (e) {
      if (e.errors) {
        profileError = Object.values(e.errors).flat().join(', ')
      } else {
        profileError = e.message
      }
    }
    profileSaving = false
  }

  let showChangePassword = $state(false)
  let currentPassword = $state('')
  let newPassword = $state('')
  let confirmNewPassword = $state('')
  let passwordSaving = $state(false)
  let passwordError = $state('')
  let passwordSuccess = $state(false)

  function openChangePassword() {
    currentPassword = ''; newPassword = ''; confirmNewPassword = ''
    passwordError = ''; passwordSuccess = false
    showChangePassword = true
  }

  async function savePassword() {
    if (!currentPassword || !newPassword) { passwordError = 'Semua kolom wajib diisi'; return }
    if (newPassword !== confirmNewPassword) { passwordError = 'Konfirmasi password tidak cocok'; return }
    if (newPassword.length < 6) { passwordError = 'Password minimal 6 karakter'; return }
    passwordSaving = true; passwordError = ''
    try {
      await api.changePassword(currentPassword, newPassword, confirmNewPassword)
      passwordSuccess = true
      setTimeout(() => showChangePassword = false, 1500)
    } catch (e) { passwordError = e.message }
    passwordSaving = false
  }

  let showAddModal = $state(false)
  let showEditAnakModal = $state(false)
  let showUpgradePopup = $state(false)
  let editingAnak = $state(null)
  let nama = $state('')
  let gender = $state('')
  let agama = $state('')
  let tanggal = $state('')
  let bulan = $state('')
  let tahun = $state('')
  let emoji = $state('👶')
  let saving = $state(false)
  let error = $state('')

  let showConfirm = $state(false)
  let confirmTitle = $state('')
  let confirmMessage = $state('')
  let confirmAction = $state(null)

  const emojiOptions = ['👶', '👦', '👧', '🧒', '👦🏻', '👧🏻', '👦🏽', '👧🏽']

  function resetForm() {
    nama = ''; gender = ''; agama = ''; tanggal = ''; bulan = ''; tahun = ''; emoji = '👶'; error = ''
  }

  function openAdd() {
    if (userRoleVal === 'developer') {
    } else if (!userPlanVal) {
      showUpgradePopup = true
      return
    } else {
      if (userRoleVal === 'trial') {
        const trialStart = userPlanVal?.subscribe_trial_at
        if (!trialStart) {
          error = 'Data trial tidak ditemukan. Silakan login ulang.'
          return
        }
        const serverNow = serverDateVal ? new Date(serverDateVal) : new Date()
        const daysDiff = Math.floor((serverNow - new Date(trialStart)) / (1000 * 60 * 60 * 24))
        if (daysDiff > trialDaysVal) {
          showUpgradePopup = true
          return
        }
      }
      if (anakListVal.length >= maxChildren) {
        showUpgradePopup = true
        return
      }
    }
    resetForm()
    showAddModal = true
  }

  function openEditAnak(anak) {
    editingAnak = anak
    nama = anak.nama || ''
    gender = anak.gender || ''
    agama = anak.agama || ''
    tanggal = anak.tanggal || (anak.tanggal_lahir != null ? String(anak.tanggal_lahir) : '')
    bulan = anak.bulan || (anak.bulan_lahir != null ? String(anak.bulan_lahir) : '')
    tahun = anak.tahun || (anak.tahun_lahir != null ? String(anak.tahun_lahir) : '')
    emoji = anak.emoji || '👶'
    error = ''
    showEditAnakModal = true
  }

  async function handleAdd() {
    if (!nama.trim()) { error = 'Nama wajib diisi'; return }
    showConfirm = true
    confirmTitle = 'Simpan Data Anak?'
    confirmMessage = 'Pastikan data anak sudah benar karena data ini tidak dapat diubah di kemudian hari.'
    confirmAction = async () => {
      showConfirm = false
      saving = true; error = ''
      try {
        await addAnak({ nama: nama.trim(), gender, agama, tanggal, bulan, tahun, emoji })
        showAddModal = false
        resetForm()
      } catch (e) { error = e.message }
      saving = false
    }
  }

  async function handleEditAnak() {
    if (!nama.trim()) { error = 'Nama wajib diisi'; return }
    showConfirm = true
    confirmTitle = 'Simpan Perubahan?'
    confirmMessage = 'Pastikan perubahan data anak sudah benar karena data anak tidak bisa diubah lagi dikemudian hari.'
    confirmAction = async () => {
      showConfirm = false
      saving = true; error = ''
      try {
        editingAnak.nama = nama.trim()
        editingAnak.gender = gender
        editingAnak.agama = agama
        editingAnak.tanggal = tanggal
        editingAnak.bulan = bulan
        editingAnak.tahun = tahun
        editingAnak.emoji = emoji
        await updateAnak(editingAnak)
        showEditAnakModal = false
      } catch (e) { error = e.message }
      saving = false
    }
  }

  async function handleDelete(anak) {
    showConfirm = true
    confirmTitle = `Hapus ${anak.nama}?`
    confirmMessage = 'Data anak dan semua aktivitasnya akan dihapus permanen.'
    confirmAction = async () => {
      showConfirm = false
      await deleteAnak(anak.id)
    }
  }

  function handleLogout() {
    showConfirm = true
    confirmTitle = 'Keluar?'
    confirmMessage = 'Yakin ingin keluar dari akun?'
    confirmAction = () => {
      showConfirm = false
      anakList.set([])
      anakToolsData.set({})
      toolsAnakId.set(null)
      selectedAnakId.set(null)
      appReady.set(false)
      authLogout()
    }
  }

  function getAvatarEmoji(gender) {
    return gender === 'female' ? '👩' : '👨'
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">

  <!-- Profile Card -->
  <div class="bg-canvas-cream rounded-[32px] border-4 border-[#B7D9BC] p-6 shadow-lg relative overflow-hidden mb-6">
    <div class="absolute top-0 right-0 w-32 h-32 bg-success-soft rounded-full -translate-y-1/2 translate-x-1/2 opacity-50"></div>
    <div class="flex items-center gap-4 relative z-10">
      <div class="w-16 h-16 rounded-full bg-success-soft flex items-center justify-center overflow-hidden border-4 border-white shadow-md shrink-0">
        <span class="text-4xl">{getAvatarEmoji(userVal?.gender)}</span>
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="font-headline-md text-text-main">{userVal?.name || '-'}</h3>
        <p class="text-sm text-on-surface-variant truncate">{userVal?.email || '-'}</p>
        {#if userVal?.phone}
          <p class="text-xs text-on-surface-variant">{userVal.phone}</p>
        {/if}
      </div>
      <button onclick={openEditProfile}
        class="w-10 h-10 rounded-full bg-white border-2 border-[#B7D9BC] flex items-center justify-center text-primary hover:bg-success-soft transition-colors shadow-sm shrink-0">
        <span class="material-symbols-outlined text-xl">edit</span>
      </button>
    </div>
    <div class="mt-4 pt-4 border-t-2 border-[#B7D9BC]/50">
      <button onclick={openChangePassword}
        class="flex items-center gap-3 text-sm text-on-surface-variant hover:text-primary transition-colors w-full">
        <span class="material-symbols-outlined text-lg">lock</span>
        <span class="font-medium">Ganti Password</span>
        <span class="ml-auto material-symbols-outlined text-base">chevron_right</span>
      </button>
    </div>
  </div>

  <!-- Anak Section -->
  <div class="mb-6">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-headline-md text-text-main flex items-center gap-2">
        <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">👶</span>
        Anak
      </h3>
      <button onclick={openAdd}
        class="px-4 py-2 rounded-xl text-sm font-bold text-white btn-pop-green-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-base">add</span> Tambah
      </button>
    </div>

    {#if !anakListVal.length}
      <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <p class="text-4xl mb-2">👶</p>
        <p class="text-sm text-on-surface-variant font-medium">Belum ada data anak</p>
      </div>
    {:else}
      <div class="space-y-3">
        {#each anakListVal as anak (anak.id)}
          <button class="bg-canvas-cream rounded-[24px] border-4 border-[#B7D9BC] shadow-md p-5 w-full text-left transition-all active:scale-[0.98] hover:border-primary/50" onclick={() => openEditAnak(anak)}>
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-full bg-success-soft flex items-center justify-center text-3xl border-2 border-white shadow-sm shrink-0">
                {anak.emoji || '👶'}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-label-lg text-text-main text-lg">{anak.nama}</p>
                <p class="text-sm text-on-surface-variant">
                  {anak.umur ? `${anak.umur} tahun` : 'Belum set umur'}
                </p>
              </div>
              <div class="w-10 h-10 rounded-full bg-white border-2 border-[#B7D9BC] flex items-center justify-center text-primary hover:bg-success-soft transition-colors shadow-sm shrink-0">
                <span class="material-symbols-outlined text-on-surface-variant">edit</span>
              </div>
            </div>
          </button>
        {/each}
      </div>
    {/if}
  </div>

  <!-- Account Section -->
  <div class="bg-canvas-cream rounded-[32px] p-6 border-4 border-[#B7D9BC] shadow-lg">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 rounded-full bg-success-soft flex items-center justify-center border-2 border-white shadow-sm">
        <span class="material-symbols-outlined text-primary">account_circle</span>
      </div>
      <div>
        <p class="font-label-lg text-text-main">Akun</p>
        <p class="text-sm text-on-surface-variant">Terhubung ke Server</p>
      </div>
    </div>
    <button onclick={handleLogout}
      class="w-full py-3 rounded-2xl text-sm font-bold border-2 border-error/30 text-error hover:bg-error/5 transition-colors flex items-center justify-center gap-2">
      <span class="material-symbols-outlined text-lg">logout</span>
      Logout
    </button>
  </div>
</div>

<!-- Edit Profile Modal -->
<AppModal show={showEditProfile} title="Edit Profil" onclose={() => showEditProfile = false}>
  {#if profileError}
    <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{profileError}</div>
  {/if}

  <div class="space-y-3">
    <input bind:value={editName}
      class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
      placeholder="Nama baru" />
    <input bind:value={editPhone}
      class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
      placeholder="Nomor telepon" />
    <div class="grid grid-cols-2 gap-2">
      <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
        {editGender === 'male' ? 'bg-blue-100 border-blue-400 text-blue-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
        onclick={() => editGender = 'male'}>👦 Ayah</button>
      <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
        {editGender === 'female' ? 'bg-pink-100 border-pink-400 text-pink-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
        onclick={() => editGender = 'female'}>👩 Bunda</button>
    </div>
  </div>

  <div class="mt-4">
    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Agama</label>
    <select bind:value={editAgama} class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white appearance-none">
      <option value="">Pilih Agama</option>
      {#each agamaOptions as a}
        <option value={a.value}>{a.label}</option>
      {/each}
    </select>
  </div>

  <div class="mt-4">
    <AppButton variant="primary" loading={profileSaving} onclick={saveProfile}>Simpan</AppButton>
  </div>
</AppModal>

<!-- Change Password Modal -->
<AppModal show={showChangePassword} title="Ganti Password" onclose={() => showChangePassword = false}>
  {#if passwordError}
    <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{passwordError}</div>
  {/if}
  {#if passwordSuccess}
    <div class="bg-success-soft text-primary rounded-xl px-4 py-3 mb-4 text-sm font-semibold">Password berhasil diubah!</div>
  {/if}

  <div class="space-y-3">
    <input type="password" bind:value={currentPassword}
      class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
      placeholder="Password saat ini" />
    <input type="password" bind:value={newPassword}
      class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
      placeholder="Password baru (min. 6 karakter)" />
    <input type="password" bind:value={confirmNewPassword}
      class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
      placeholder="Konfirmasi password baru" />
  </div>

  <div class="mt-4">
    <AppButton variant="primary" loading={passwordSaving} onclick={savePassword}>Simpan Password</AppButton>
  </div>
</AppModal>

<!-- Add Anak Modal -->
<AppModal show={showAddModal} title="" onclose={() => showAddModal = false}>
  <div class="flex items-center gap-2 mb-1">
    <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">👶</span>
    <h3 class="font-headline-md text-text-main">Tambah Anak</h3>
  </div>

  {#if error}
    <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{error}</div>
  {/if}

  <div class="space-y-4 mt-4">
    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Nama</label>
      <input bind:value={nama}
        class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
        placeholder="Nama anak" />
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Gender</label>
      <div class="grid grid-cols-2 gap-2">
        <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
          {gender === 'male' ? 'bg-blue-100 border-blue-400 text-blue-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
          onclick={() => gender = 'male'}>👦 Laki-laki</button>
        <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
          {gender === 'female' ? 'bg-pink-100 border-pink-400 text-pink-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
          onclick={() => gender = 'female'}>👧 Perempuan</button>
      </div>
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Agama</label>
      <select bind:value={agama} class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white appearance-none">
        <option value="">Pilih Agama</option>
        {#each agamaOptions as a}
          <option value={a.value}>{a.label}</option>
        {/each}
      </select>
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
      <div class="grid grid-cols-3 gap-2">
        <select bind:value={tanggal} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white">
          <option value="" disabled>Tgl</option>
          {#each Array(31) as _, i}
            <option value={i + 1}>{i + 1}</option>
          {/each}
        </select>
        <select bind:value={bulan} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white">
          <option value="" disabled>Bulan</option>
          {#each ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as m, i}
            <option value={i + 1}>{m}</option>
          {/each}
        </select>
        <select bind:value={tahun} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white">
          <option value="" disabled>Tahun</option>
          {#each Array(20) as _, i}
            <option value={2026 - i}>{2026 - i}</option>
          {/each}
        </select>
      </div>
    </div>
  </div>

  <div class="flex gap-3 mt-6">
    <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={() => showAddModal = false}>
      Batal
    </button>
    <button class="flex-1 py-3 rounded-2xl text-white text-sm font-bold btn-pop-green" onclick={handleAdd} disabled={saving}>
      {saving ? 'Menyimpan...' : 'Simpan'}
    </button>
  </div>
</AppModal>

<!-- Edit Anak Modal -->
<AppModal show={showEditAnakModal} title="" onclose={() => showEditAnakModal = false}>
  <div class="flex items-center gap-2 mb-1">
    <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">✏️</span>
    <h3 class="font-headline-md text-text-main">Edit Profil Anak</h3>
  </div>

  {#if error}
    <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{error}</div>
  {/if}

  <div class="space-y-4 mt-4">
    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Nama</label>
      <input bind:value={nama}
        class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
        placeholder="Nama anak" />
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Gender</label>
      <div class="grid grid-cols-2 gap-2">
        <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
          {gender === 'male' ? 'bg-blue-100 border-blue-400 text-blue-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
          onclick={() => gender = 'male'}>👦 Laki-laki</button>
        <button class="py-2.5 rounded-xl text-sm font-bold border-2 transition-all
          {gender === 'female' ? 'bg-pink-100 border-pink-400 text-pink-700' : 'border-[#B7D9BC] text-on-surface-variant bg-white'}"
          onclick={() => gender = 'female'}>👧 Perempuan</button>
      </div>
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Agama</label>
      <select bind:value={agama} class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white appearance-none">
        <option value="">Pilih Agama</option>
        {#each agamaOptions as a}
          <option value={a.value}>{a.label}</option>
        {/each}
      </select>
    </div>

    <div>
      <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
      <div class="grid grid-cols-3 gap-2">
        <select bind:value={tanggal} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white {tanggal ? 'text-text-main' : 'text-on-surface-variant'}">
          <option value="">Tgl</option>
          {#each Array(31) as _, i}
            <option value={String(i + 1)}>{i + 1}</option>
          {/each}
        </select>
        <select bind:value={bulan} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white {bulan ? 'text-text-main' : 'text-on-surface-variant'}">
          <option value="">Bulan</option>
          {#each ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as m, i}
            <option value={String(i + 1)}>{m}</option>
          {/each}
        </select>
        <select bind:value={tahun} class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white {tahun ? 'text-text-main' : 'text-on-surface-variant'}">
          <option value="">Tahun</option>
          {#each Array(20) as _, i}
            <option value={String(2026 - i)}>{2026 - i}</option>
          {/each}
        </select>
      </div>
    </div>
  </div>

  <div class="flex gap-3 mt-6">
    <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={() => showEditAnakModal = false}>
      Batal
    </button>
    <button class="flex-1 py-3 rounded-2xl text-white text-sm font-bold btn-pop-green" onclick={handleEditAnak} disabled={saving}>
      {saving ? 'Menyimpan...' : 'Simpan'}
    </button>
  </div>
</AppModal>

<style>
  .btn-pop-green-sm {
    background-color: #6DBE7B;
    box-shadow: 0 3px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green-sm:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 3px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-gray {
    background-color: #e5e5e5;
    box-shadow: 0 3px 0 #999;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #999;
  }
</style>

{#if showUpgradePopup}
  <div class="fixed inset-0 z-[100] flex items-end justify-center lg:items-center">
    <div class="absolute inset-0 bg-black/40" onclick={() => showUpgradePopup = false}></div>
    <div class="relative bg-canvas-cream rounded-t-[32px] lg:rounded-[32px] w-full max-w-md p-6 pb-8 lg:mb-0 border-4 border-primary border-b-0 lg:border-b-4">
      <div class="w-10 h-1 bg-primary/30 rounded-full mx-auto mb-5 lg:hidden"></div>
      <div class="text-center">
        <div class="w-16 h-16 rounded-full bg-success-soft flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-md">
          <span class="material-symbols-outlined text-3xl text-primary">workspace_premium</span>
        </div>
        <h3 class="font-headline-md text-text-main mb-2">Upgrade Paket</h3>
        <p class="text-sm text-on-surface-variant mb-1">Kamu sudah mencapai batas <span class="font-bold text-primary">{maxChildren} anak</span> untuk paket yang kamu pilih saat ini.</p>
        <p class="text-xs text-on-surface-variant mb-6">Upgrade paket untuk menambah lebih banyak anak dan fitur premium lainnya.</p>
        <div class="flex gap-3">
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={() => showUpgradePopup = false}>
            Nanti Saja
          </button>
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green" onclick={() => { showUpgradePopup = false; switchTab('billing') }}>
            Lihat Paket
          </button>
        </div>
      </div>
    </div>
  </div>
{/if}

{#if showConfirm}
  <div class="fixed inset-0 z-[110] flex items-end justify-center lg:items-center">
    <div class="absolute inset-0 bg-black/50" onclick={() => showConfirm = false}></div>
    <div class="relative bg-canvas-cream rounded-t-[32px] lg:rounded-[32px] w-full max-w-sm p-6 pb-8 lg:mb-0 border-4 border-[#B7D9BC] shadow-xl">
      <div class="w-10 h-1 bg-outline-variant rounded-full mx-auto mb-5 lg:hidden"></div>
      <div class="text-center">
        <div class="w-16 h-16 rounded-full bg-warning-soft flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-md">
          <span class="material-symbols-outlined text-3xl text-warm-bonding">help</span>
        </div>
        <h3 class="font-headline-md text-text-main mb-2">{confirmTitle}</h3>
        <p class="text-sm text-on-surface-variant mb-6">{confirmMessage}</p>
        <div class="flex gap-3">
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={() => showConfirm = false}>
            Batal
          </button>
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green" onclick={confirmAction}>
            Ya, Lanjutkan
          </button>
        </div>
      </div>
    </div>
  </div>
{/if}
