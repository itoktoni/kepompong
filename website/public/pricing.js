// pricing.js - Standalone pricing loader from API
(function() {
  function formatPrice(price) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(price);
  }

  function renderPlans(plans) {
    const containerEl = document.getElementById('pricing-container');
    const loadingEl = document.getElementById('pricing-loading');
    if (!containerEl) return;

    const sortedPlans = [
      ...plans.filter(function(p) { return !p.recommended; }),
      ...plans.filter(function(p) { return p.recommended; })
    ];

    containerEl.innerHTML = sortedPlans.map(function(plan) {
      var features = plan.features || [];
      var featuresHtml = features.map(function(f) {
        return '<li class="flex items-center gap-3"><span class="material-symbols-outlined text-primary text-sm">check_circle</span>' + f + '</li>';
      }).join('');

      var strikethroughHtml = '';
      if (plan.price_strikethrough) {
        strikethroughHtml = '<span class="block text-label-sm text-on-surface-variant line-through opacity-60">' + formatPrice(plan.price_strikethrough) + '</span>';
      }

      var badgeHtml = '';
      if (plan.recommended) {
        badgeHtml = '<div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-on-primary px-4 py-1 rounded-full text-label-sm font-normal">REKOMENDASI BUNDA</div>';
      }

      var cardClass = plan.recommended
        ? 'border-2 border-primary shadow-xl md:scale-105 rounded-3xl bg-primary/5'
        : 'border border-outline-variant bg-white';

      var titleClass = plan.recommended ? '' : 'text-on-surface';
      var priceClass = plan.recommended ? 'text-primary' : '';
      var btnClass = plan.recommended
        ? 'bg-primary text-on-primary shadow-lg hover:opacity-90 py-4'
        : 'border border-primary text-primary hover:bg-primary/5';
      var btnText = plan.recommended ? 'Langganan Sekarang' : 'Pilih Paket';

      return '<div class="relative p-8 rounded-2xl shadow-sm hover:shadow-md transition-all ' + cardClass + '">' +
        badgeHtml +
        '<h3 class="font-headline-md text-headline-md mb-2 font-fredoka ' + titleClass + '">' + plan.name + '</h3>' +
        '<p class="text-on-surface-variant text-label-lg mb-6">' + plan.description + '</p>' +
        '<div class="mb-8">' +
          '<span class="text-display-lg-mobile font-bold font-fredoka ' + priceClass + '">' + formatPrice(plan.price) + '</span>' +
          strikethroughHtml +
        '</div>' +
        '<ul class="space-y-4 mb-10">' + featuresHtml + '</ul>' +
        '<a href="/#cta" class="w-full py-3 rounded-xl text-lg transition-colors font-fredoka text-center block ' + btnClass + '">' + btnText + '</a>' +
      '</div>';
    }).join('');

    if (loadingEl) loadingEl.classList.add('hidden');
    containerEl.classList.remove('hidden');
  }

  function loadPricing() {
    var backendUrl = window.PRICING_API_URL;
    var containerEl = document.getElementById('pricing-container');
    if (!containerEl) return;

    // Must have valid backend URL
    if (!backendUrl) {
      // Show error state - no hardcoded fallback
      var loadingEl = document.getElementById('pricing-loading');
      if (loadingEl) loadingEl.classList.add('hidden');
      containerEl.classList.remove('hidden');
      containerEl.innerHTML = '<div class="col-span-3 text-center py-8 text-on-surface-variant"><p class="font-fredoka">Pricing tidak tersedia saat ini.</p></div>';
      return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', backendUrl, true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        var loadingEl = document.getElementById('pricing-loading');
        if (xhr.status === 200) {
          try {
            var data = JSON.parse(xhr.responseText);
            var plans = data.plans || [];
            if (plans.length > 0) {
              renderPlans(plans);
            } else {
              containerEl.innerHTML = '<div class="col-span-3 text-center py-8 text-on-surface-variant"><p class="font-fredoka">Pricing tidak tersedia saat ini.</p></div>';
              containerEl.classList.remove('hidden');
              if (loadingEl) loadingEl.classList.add('hidden');
            }
          } catch (e) {
            containerEl.innerHTML = '<div class="col-span-3 text-center py-8 text-on-surface-variant"><p class="font-fredoka">Gagal memuat pricing. Silakan refresh.</p></div>';
            containerEl.classList.remove('hidden');
            if (loadingEl) loadingEl.classList.add('hidden');
          }
        } else {
          containerEl.innerHTML = '<div class="col-span-3 text-center py-8 text-on-surface-variant"><p class="font-fredoka">Tidak dapat memuat paket. Silakan refresh halaman.</p></div>';
          containerEl.classList.remove('hidden');
          if (loadingEl) loadingEl.classList.add('hidden');
        }
      }
    };
    xhr.onerror = function() {
      var loadingEl = document.getElementById('pricing-loading');
      containerEl.innerHTML = '<div class="col-span-3 text-center py-8 text-on-surface-variant"><p class="font-fredoka">Tidak dapat memuat paket. Silakan refresh halaman.</p></div>';
      containerEl.classList.remove('hidden');
      if (loadingEl) loadingEl.classList.add('hidden');
    };
    xhr.send();
  }

  // Run when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadPricing);
  } else {
    loadPricing();
  }
})();
