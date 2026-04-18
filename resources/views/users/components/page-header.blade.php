{{-- Page Header Component --}}
{{-- Usage: @include('components.page-header', ['title' => 'About Us', 'breadcrumb' => 'About']) --}}

<div class="container-fluid page-header wow fadeIn" data-wow-delay="0.1s">
    <div class="container">
        <h1 class="display-3 mb-3 animated slideInDown">{{ $title ?? 'Page Title' }}</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a class="text-white" href="{{ route('home') }}">Home</a>
                </li>
                @if(isset($parent))
                    <li class="breadcrumb-item">
                        <a class="text-white" href="{{ $parentUrl ?? '#' }}">{{ $parent }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item text-white active" aria-current="page">
                    {{ $breadcrumb ?? $title ?? 'Page' }}
                </li>
            </ol>
        </nav>
    </div>
</div>