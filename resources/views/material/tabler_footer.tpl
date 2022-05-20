<script>
    // Initialize the agent at application startup.
    // You can also use https://openfpcdn.io/fingerprintjs/v3/esm.min.js
    const fpPromise = import('/theme/tabler/js/esm.min.js')
        .then(FingerprintJS => FingerprintJS.load())

    // Get the visitor identifier when you need it.
    fpPromise
        .then(fp => fp.get())
        .then(result => {
            // This is the visitor identifier:
            visitorId = result.visitorId
        })
</script>
{if $config['show_live_chat_on_logout_page'] == true}
    {include file='live_chat.tpl'}
{/if}