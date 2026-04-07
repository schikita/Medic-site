<div class="xr-hubspot-modal" id="xr-hubspot-modal" hidden data-hubspot-modal>
    <div class="xr-hubspot-modal__backdrop" data-hubspot-close></div>
    <div class="xr-hubspot-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="xr-hubspot-title">
        <button type="button" class="xr-hubspot-modal__x" data-hubspot-close aria-label="Close">×</button>
        <h2 id="xr-hubspot-title" class="xr-hubspot-modal__title">Form</h2>
        <iframe class="xr-hubspot-modal__frame" title="HubSpot" data-hubspot-iframe></iframe>
        <p class="xr-hubspot-modal__fallback" data-hubspot-fallback hidden>Укажите URL формы HubSpot в админке (hubspot.whitepaper_url / demo_url).</p>
    </div>
</div>
<button class="xr-back-top" id="xr-back-top" aria-label="Back to top" hidden>
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
        <path d="M9 14V4M9 4L4 9M9 4l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>
<script type="application/json" id="xr-hubspot-config"><?= json_encode($site['hubspot'] ?? [], JSON_UNESCAPED_UNICODE) ?></script>
    <script src="/assets/js/main.js" defer></script>
    <script src="/assets/js/blocks.js" defer></script>
</body>
</html>
