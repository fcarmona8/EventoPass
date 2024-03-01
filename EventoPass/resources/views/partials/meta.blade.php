{{-- Títol de la pàgina --}}
<title>{{ $title ?? 'Títol Predeterminat del Lloc Web' }}</title>
<meta name="description" content="{{ $description ?? 'Descripció predeterminada del lloc web.' }}">
<meta name="keywords" content="{{ $keywords ?? 'paraules, clau, predeterminades' }}">

{{-- Open Graph Meta per a la compartició a Xarxes Socials --}}
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:url" content="{{ $ogUrl ?? request()->url() }}">
<meta property="og:title" content="{{ $ogTitle ?? ($title ?? 'Títol Predeterminat del Lloc Web') }}">
<meta property="og:description"
    content="{{ $ogDescription ?? ($description ?? 'Descripció predeterminada del lloc web.') }}">
<meta property="og:image" content="{{ $ogImage ?? asset('path/to/default/image.jpg') }}">

{{-- Twitter Card Meta --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $twitterUrl ?? request()->url() }}">
<meta name="twitter:title" content="{{ $twitterTitle ?? ($title ?? 'Títol Predeterminat del Lloc Web') }}">
<meta name="twitter:description"
    content="{{ $twitterDescription ?? ($description ?? 'Descripció predeterminada del lloc web.') }}">
<meta name="twitter:image" content="{{ $twitterImage ?? ($ogImage ?? asset('path/to/default/image.jpg')) }}">
