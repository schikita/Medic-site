<div class="xr-hubspot-modal" id="xr-hubspot-modal" hidden data-hubspot-modal>
    <div class="xr-hubspot-modal__backdrop" data-hubspot-close></div>
    <div class="xr-hubspot-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="xr-hubspot-title">
        <button type="button" class="xr-hubspot-modal__x" data-hubspot-close aria-label="Close">×</button>
        <h2 id="xr-hubspot-title" class="xr-hubspot-modal__title">Form</h2>
        <iframe class="xr-hubspot-modal__frame" title="HubSpot" data-hubspot-iframe></iframe>
        <p class="xr-hubspot-modal__fallback" data-hubspot-fallback hidden>Укажите URL формы HubSpot в админке (hubspot.whitepaper_url / demo_url).</p>
    </div>
</div>
<script type="application/json" id="xr-hubspot-config"><?= json_encode($site['hubspot'] ?? [], JSON_UNESCAPED_UNICODE) ?></script>
    <script src="/assets/js/main.js" defer></script>
    <script src="/assets/js/blocks.js" defer></script>
</body>
</html>
