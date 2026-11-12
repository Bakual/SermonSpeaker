document.addEventListener('click', (e) => {
    const el = e.target.closest('[data-ss-event]');

    if (!el) {
        return;
    }

    const detail = {
        action: el.dataset.ssEvent,
        type:   el.dataset.ssType || '',
        id:     el.dataset.ssId || '',
        file:   el.dataset.ssFile || '',
    };

    // Hook für eigene Integrationen (Matomo, Consent-Tools, ...)
    document.dispatchEvent(new CustomEvent('sermonspeaker:track', { detail }));

    if (typeof window.gtag === 'function') {
        window.gtag('event', 'file_download', {
            file_name:      detail.file || ('sermon-' + detail.id),
            content_type:   detail.type,
            item_id:        detail.id,
            transport_type: 'beacon',
        });
    } else if (Array.isArray(window.dataLayer)) {
        window.dataLayer.push({ event: 'sermonspeaker_' + detail.action, ...detail });
    }
});