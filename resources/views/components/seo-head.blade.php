@php
    $siteName = 'Abhix AI';
    $siteDescription = 'Abhix AI is a powerful AI assistant built with Laravel.';
    $siteUrl = rtrim(config('app.url', url('/')), '/');
    $logoUrl = asset('logo.png');
@endphp

<meta name="application-name" content="{{ $siteName }}">
<link rel="canonical" href="{{ $siteUrl }}">

<link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="@yield('title', $siteName . ' - AI Assistant')">
<meta property="og:description" content="{{ $siteDescription }}">
<meta property="og:url" content="{{ $siteUrl }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ $logoUrl }}">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="@yield('title', $siteName . ' - AI Assistant')">
<meta name="twitter:description" content="{{ $siteDescription }}">
<meta name="twitter:image" content="{{ $logoUrl }}">

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'alternateName' => ['ABX GPT', 'Abhix AI Assistant'],
    'url' => $siteUrl,
    'description' => $siteDescription,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
