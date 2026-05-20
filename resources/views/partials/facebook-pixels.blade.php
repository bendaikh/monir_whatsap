@php
    $fbPixels = $store->activeFacebookPixels();
@endphp
@if(count($fbPixels) > 0)
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
    @foreach($fbPixels as $pixelId)
    fbq('init', '{{ $pixelId }}');
    @endforeach
    fbq('track', 'PageView');
    @if(isset($trackViewContent) && $trackViewContent && isset($product))
    fbq('track', 'ViewContent', {
        content_name: '{{ addslashes($product->name) }}',
        content_ids: ['{{ $product->id }}'],
        content_type: 'product',
        value: {{ $product->price }},
        currency: '{{ $product->landing_page_currency ?? 'MAD' }}'
    });
    @endif
    @if(isset($trackLead) && $trackLead && isset($leadValue))
    fbq('track', 'Lead', {
        @if(isset($product))
        content_name: '{{ addslashes($product->name) }}',
        content_ids: ['{{ $product->id }}'],
        content_type: 'product',
        @endif
        value: {{ $leadValue }},
        currency: '{{ $leadCurrency ?? 'MAD' }}'
    });
    @endif
</script>
@foreach($fbPixels as $pixelId)
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/></noscript>
@endforeach
@endif
