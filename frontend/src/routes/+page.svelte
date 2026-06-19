<script>
  import { onMount } from 'svelte'
  import { get } from 'svelte/store'
  import { initInstall, canInstall, installApp } from '$lib/composables/useInstall.js'
  import { downloadAllData, getSetting, saveSetting } from '$lib/db.js'
  import * as appStore from '$lib/stores/appStore.js'
  import * as authStore from '$lib/stores/authStore.js'
  import * as anakStore from '$lib/stores/anakStore.js'
  import * as toolsStore from '$lib/stores/toolsStore.js'
  import * as activityStore from '$lib/stores/activityStore.js'
  import * as api from '$lib/services/api.js'
  import { buildAktivitasDataFromAPI, setAktivitasData } from '$lib/data/activities.js'
  import { fetchNotifications, initRealtime, disconnectRealtime } from '$lib/composables/useNotifications.js'
  import { isOffline } from '$lib/utils/network.js'

  import AppHeader from '$lib/layouts/AppHeader.svelte'
  import DesktopHeader from '$lib/layouts/DesktopHeader.svelte'
  import AppSidebar from '$lib/layouts/AppSidebar.svelte'

  initInstall()
  import BottomNav from '$lib/layouts/BottomNav.svelte'
  import SyncModal from '$lib/components/SyncModal.svelte'
  import LoginPage from '$lib/pages/LoginPage.svelte'
  import RegisterPage from '$lib/pages/RegisterPage.svelte'
  import ReferralPage from '$lib/pages/ReferralPage.svelte'
  import VerificationPage from '$lib/pages/VerificationPage.svelte'
  import PilarTab from '$lib/pages/PilarTab.svelte'
  import ActivityTab from '$lib/pages/ActivityTab.svelte'
  import ProgressTab from '$lib/pages/ProgressTab.svelte'
  import ProfileTab from '$lib/pages/ProfileTab.svelte'
  import SettingsTab from '$lib/pages/SettingsTab.svelte'
  import BillingTab from '$lib/pages/BillingTab.svelte'
  import AffiliateTab from '$lib/pages/AffiliateTab.svelte'
  import ChallengeTab from '$lib/pages/ChallengeTab.svelte'
  import JadwalTab from '$lib/pages/JadwalTab.svelte'
  import ChecklistTab from '$lib/pages/ChecklistTab.svelte'
  import GenerateIdeaPage from '$lib/pages/GenerateIdeaPage.svelte'

  let currentTab = $state('activity')
  let currentPilar = $state(null)
  let currentAnakId = $state(null)
  let currentUserName = $state('Parent')
  let currentUserGender = $state('')
  let currentUserEmail = $state('')
  let isAuth = $state(false)
  let showSyncModal = $state(false)
  let ready = $state(false)
  let isReferral = $state(false)
  let referralCode = $state('')
  let initialRegister = $state(false)
  let showRegister = $state(false)
  let currentToolsAnakId = $state(null)
  let toolsAnakList = $state([])
  let canInstallVal = $state(false)
  let showMobileMenu = $state(false)
  let userPlanVal = $state(null)
  let userRoleVal = $state('')
  let needsVerificationVal = $state(false)

  // Subscribe to stores
  $effect(() => {
    const unsubTab = appStore.activeTab.subscribe(v => currentTab = v)
    const unsubPilar = appStore.selectedPilar.subscribe(v => currentPilar = v)
    const unsubAnakId = appStore.selectedAnakId.subscribe(v => currentAnakId = v)
    const unsubUserName = appStore.userName.subscribe(v => currentUserName = v)
    const unsubUserGender = appStore.userGender.subscribe(v => currentUserGender = v)
    const unsubAuth = authStore.isAuthenticated.subscribe(v => isAuth = v)
    const unsubReady = appStore.appReady.subscribe(v => ready = v)
    const unsubToolsId = toolsStore.toolsAnakId.subscribe(v => currentToolsAnakId = v)
    const unsubCanInstall = canInstall.subscribe(v => canInstallVal = v)
    const unsubUser = authStore.user.subscribe(v => {
      if (v) {
        currentUserEmail = v.email || ''
      }
    })
    const unsubUserPlan = authStore.userPlan.subscribe(v => userPlanVal = v)
    const unsubUserRole = authStore.userRole.subscribe(v => userRoleVal = v)
    const unsubNeedsVerification = authStore.needsVerification.subscribe(v => needsVerificationVal = v)
    const unsubAnakList = anakStore.anakList.subscribe(async v => {
      toolsAnakList = v
      if (v.length && get(authStore.isAuthenticated)) {
        const currentSelected = get(appStore.selectedAnakId)
        if (!currentSelected || !v.find(a => a.id === currentSelected)) {
          appStore.selectedAnakId.set(v[0].id)
        }
        const currentTools = get(toolsStore.toolsAnakId)
        if (!currentTools || !v.find(a => a.id === currentTools)) {
          toolsStore.toolsAnakId.set(v[0].id)
        }
        await toolsStore.loadToolsData(v)
      }
    })

    return () => {
      unsubTab(); unsubPilar(); unsubAnakId(); unsubUserName(); unsubUserGender()
      unsubAuth(); unsubReady(); unsubToolsId(); unsubCanInstall(); unsubUser()
      unsubUserPlan(); unsubUserRole(); unsubNeedsVerification(); unsubAnakList()
    }
  })

  const pageTitle = $derived(({
    pilar: 'Soft skills', progress: 'Statistik', activity: 'Aktivitas',
    profile: 'Profile', settings: 'Pengaturan', billing: 'Billing',
    referral: 'Affiliate', challenge: 'Challenge', jadwal: 'Jadwal Harian',
    checklist: 'Checklist Harian'
  }[currentTab] || 'Jejak Tumbuh'))

  const trialExpired = $derived(!!(userPlanVal?.expired && userRoleVal === 'trial'))

  const justPaid = $derived.by(() => {
    if (typeof localStorage === 'undefined') return false
    const ts = localStorage.getItem('lk_just_paid')
    if (!ts) return false
    return Date.now() - Number(ts) < 60000
  })

  const noSubscribe = $derived(isAuth && ready && (!userPlanVal || userPlanVal?.expired) && !justPaid)
  const noAnak = $derived(isAuth && toolsAnakList.length === 0 && ready)

  $effect(() => {
    if (noSubscribe && currentTab !== 'billing') {
      appStore.switchTab('billing')
    }
  })

  function handleTrialGuard() {
    if ((trialExpired || noSubscribe) && currentTab !== 'billing') {
      appStore.switchTab('billing')
      return true
    }
    if (noAnak && currentTab !== 'profile' && currentTab !== 'billing' && currentTab !== 'referral') {
      appStore.switchTab('profile')
      return true
    }
    return false
  }

  // Check referral
  if (typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search)
    isReferral = urlParams.has('ref')
    if (urlParams.has('ref')) {
      referralCode = urlParams.get('ref')
      localStorage.setItem('lk_ref_code', referralCode)
    }
    if (urlParams.has('action') && urlParams.get('action') === 'register') {
      initialRegister = true
      showRegister = true
    }
  }

  async function onLoginSuccess(data) {
    if (get(authStore.needsVerification)) return

    try {
      const me = await api.getMe()
      authStore.applyServerData(me)
    } catch {}

    const plan = get(authStore.userPlan)
    if (!plan || plan.expired) {
      appStore.switchTab('billing')
    } else {
      appStore.switchTab('activity')
    }
    await downloadAllData(data)
    await seedAndLoad()
  }

  async function onVerificationSuccess(data) {
    try {
      const me = await api.getMe()
      authStore.applyServerData(me)
    } catch {}

    const plan = get(authStore.userPlan)
    if (!plan || plan.expired) {
      appStore.switchTab('billing')
    } else {
      appStore.switchTab('activity')
    }

    const serverList = get(authStore.serverAnakList)
    if (serverList.length) {
      await downloadAllData({ anak_list: serverList })
    }
    await seedAndLoad()
  }

  function handleLogout() {
    disconnectRealtime()
    anakStore.anakList.set([])
    toolsStore.anakToolsData.set({})
    toolsStore.toolsAnakId.set(null)
    appStore.selectedAnakId.set(null)
    appStore.appReady.set(false)
    authStore.logout()
  }

  async function onSynced() {
    await seedAndLoad()
  }

  async function seedAndLoad() {
    const currentUser = get(authStore.user)
    if (!currentUser) return

    await anakStore.validateAndClearIfDifferentUser(currentUser.id)

    if (currentUser.name) {
      appStore.userName.set(currentUser.name)
      appStore.userGender.set(currentUser.gender || '')
    }

    await anakStore.loadAnakList()
    const list = get(anakStore.anakList)
    await toolsStore.loadToolsData(list)

    await activityStore.loadFromCache()
    const cache = get(activityStore.activitiesCache)
    if (cache) {
      const aktivitas = buildAktivitasDataFromAPI(cache)
      setAktivitasData(aktivitas)
    }

    const pilarCache = await getSetting('pilars_skills_cache')
    if (pilarCache?.pilars) authStore.pilars.set(pilarCache.pilars)
    if (pilarCache?.skills) authStore.skills.set(pilarCache.skills)

    if (!get(appStore.selectedAnakId) && list.length) {
      appStore.selectedAnakId.set(list[0].id)
    }
    appStore.appReady.set(true)

    const plan = get(authStore.userPlan)
    const role = get(authStore.userRole)
    const isExpired = !!plan?.expired
    const noSub = !plan || isExpired
    if (noSub) {
      appStore.switchTab('billing')
    } else if (!list.length) {
      appStore.switchTab('profile')
    }

    if (!isOffline()) {
      fetchNotifications()
      initRealtime(currentUser.id)
    }
  }

  onMount(() => {
    appStore.initBackHandler()

    if (get(authStore.isAuthenticated)) {
      if (isOffline()) {
        seedAndLoad()
        return
      }

      api.getMe().then(async (me) => {
        authStore.applyServerData(me)
        await downloadAllData(me)
        if (!get(authStore.needsVerification)) {
          seedAndLoad()
        }
      }).catch((err) => {
        if (err.needs_verification) {
          authStore.needsVerification.set(true)
          authStore.verificationGateway.set(err.verification_gateway || 'whatsapp')
        } else if (err.message?.includes('Unauthorized')) {
          authStore.logout()
        } else {
          seedAndLoad()
        }
      })
    }
  })


</script>

{#if isReferral}
  <ReferralPage referralCode={referralCode} />
{:else if showRegister && !isAuth}
  <RegisterPage onsuccess={onLoginSuccess} initialReferralCode={referralCode} />
{:else if !isAuth}
  <LoginPage onsuccess={onLoginSuccess} initialRegister={initialRegister} initialReferralCode={referralCode} />
{:else}
  <div class="bg-canvas-cream text-text-main min-h-screen">
    <AppSidebar
      activeTab={currentTab}
      userName={currentUserName}
      userGender={currentUserGender}
      showMobileMenu={showMobileMenu}
      onswitch={(tab) => { if (!handleTrialGuard()) appStore.switchTab(tab) }}
      oncloseMobile={() => showMobileMenu = false}
    />

    <DesktopHeader
      title={pageTitle}
      canInstallProp={canInstallVal}
      userName={currentUserName}
      userEmail={currentUserEmail}
      userGender={currentUserGender}
      onsync={() => showSyncModal = true}
      oninstall={installApp}
      onprofile={() => appStore.switchTab('profile')}
      onsettings={() => appStore.switchTab('settings')}
      onbilling={() => appStore.switchTab('billing')}
      onreferral={() => appStore.switchTab('referral')}
      onlogout={handleLogout}
    />

    <AppHeader
      title={pageTitle}
      activeTab={currentTab}
      userName={currentUserName}
      userGender={currentUserGender}
      userEmail={currentUserEmail}
      canInstallProp={canInstallVal}
      onswitch={(tab) => { if (!handleTrialGuard()) appStore.switchTab(tab) }}
      onsync={() => showSyncModal = true}
      oninstall={installApp}
      onprofile={() => appStore.switchTab('profile')}
      onsettings={() => appStore.switchTab('settings')}
      onbilling={() => appStore.switchTab('billing')}
      onreferral={() => appStore.switchTab('referral')}
      onlogout={handleLogout}
      onopenMobileMenu={() => showMobileMenu = true}
    />

    <main class="content-wrapper pb-24 lg:pb-8" onclick={handleTrialGuard} role="presentation">
      {#if (trialExpired || noSubscribe) && currentTab !== 'billing'}
        <div class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-6" onclick={(e) => { e.stopPropagation(); appStore.switchTab('billing') }}>
          <div class="bg-canvas-cream rounded-[28px] p-6 border-4 border-error/30 shadow-xl max-w-sm w-full text-center" onclick={(e) => e.stopPropagation()}>
            <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center mx-auto mb-4">
              <span class="text-4xl text-error">{noSubscribe ? '🏆' : '⏰'}</span>
            </div>
            <h3 class="font-bold text-lg text-text-main mb-2">{noSubscribe ? 'Pilih Paket' : 'Masa Trial Berakhir'}</h3>
            <p class="text-sm text-on-surface-variant mb-5">{noSubscribe ? 'Anda belum memiliki paket aktif. Silakan pilih paket untuk melanjutkan menggunakan Jejak Tumbuh.' : 'Masa trial Anda telah berakhir. Silakan upgrade paket untuk melanjutkan menggunakan Jejak Tumbuh.'}</p>
            <button onclick={(e) => { e.stopPropagation(); appStore.switchTab('billing') }}
              class="w-full py-3 rounded-2xl bg-primary text-sm font-bold text-white btn-pop-green">
              {noSubscribe ? 'Pilih Paket' : 'Upgrade Sekarang'}
            </button>
          </div>
        </div>
      {/if}
      {#if currentTab === 'pilar'}
        <PilarTab />
      {:else if currentTab === 'activity'}
        <ActivityTab />
      {:else if currentTab === 'progress'}
        <ProgressTab />
      {:else if currentTab === 'profile'}
        <ProfileTab />
      {:else if currentTab === 'settings'}
        <SettingsTab />
      {:else if currentTab === 'billing'}
        <BillingTab />
      {:else if currentTab === 'referral'}
        <AffiliateTab />
      {:else if currentTab === 'challenge'}
        <ChallengeTab />
      {:else if currentTab === 'jadwal'}
        <JadwalTab />
      {:else if currentTab === 'checklist'}
        <ChecklistTab />
      {:else if currentTab === 'generate-idea'}
        <GenerateIdeaPage />
      {/if}
    </main>

    <BottomNav activeTab={currentTab} onswitch={(tab) => { if (!handleTrialGuard()) appStore.switchTab(tab) }} />

    <SyncModal show={showSyncModal} onclose={() => showSyncModal = false} onsynced={onSynced} />

    {#if needsVerificationVal}
      <VerificationPage inline={true} onsuccess={onVerificationSuccess} />
    {/if}
  </div>
{/if}
