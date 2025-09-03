@php
    use Illuminate\Support\Str;

    // Determine if we're in the admin area by checking the current route name.
    $currentRoute = Route::currentRouteName();
    $isAdmin      = Str::startsWith($currentRoute, 'admin.');
@endphp

@if ($isAdmin)


<!-- Breadcrumb -->
<ul class="breadcrumbs mb-3">
    @foreach ($breadcrumbs as $breadcrumb)
        @if ($breadcrumb->url && $loop->first)
            <li class="nav-home">
                <a href="{{ $breadcrumb->url }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
        @elseif ($breadcrumb->url && !$loop->last)
            <li class="nav-item">
                <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
        @else
            <li class="nav-item">
                <a href="#">{{ $breadcrumb->title }}</a>
            </li>
        @endif
    @endforeach
</ul>

@else
    {{-- FRONTEND BREADCRUMBS --}}
    <div class="breadcrumb_content style2">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->first)
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb->url ?? '#' }}">{{ $breadcrumb->title }}</a>
                    </li>
                @elseif (!$loop->last)
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb->url ?? '#' }}">{{ $breadcrumb->title }}</a>
                    </li>
                @else
                    <li class="breadcrumb-item active text-thm" aria-current="page">
                        {{ $breadcrumb->title }}
                    </li>
                @endif
            @endforeach
        </ol>
        @php
        $lastCrumb = collect($breadcrumbs)->last();
        $pageTitle = $lastCrumb->header ?? null;
    @endphp

    @if ($pageTitle)
        <h2 class="breadcrumb_title">{{ $pageTitle }}</h2>
    @endif


    </div>
@endif
