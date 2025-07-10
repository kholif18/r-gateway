@props(['paginator'])

@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $paginationRange = 2;
@endphp

<div class="pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
    @endif

    {{-- Ellipsis logic --}}
    @if ($currentPage > 3)
        <a href="{{ $paginator->url(1) }}" class="page-btn">1</a>
        @if ($currentPage > 4)
            <span class="page-btn disabled">...</span>
        @endif
    @endif

    @for ($i = max(1, $currentPage - $paginationRange); $i <= min($lastPage, $currentPage + $paginationRange); $i++)
        @if ($i == $currentPage)
            <span class="page-btn active">{{ $i }}</span>
        @else
            <a href="{{ $paginator->url($i) }}" class="page-btn">{{ $i }}</a>
        @endif
    @endfor

    @if ($currentPage < $lastPage - 2)
        @if ($currentPage < $lastPage - 3)
            <span class="page-btn disabled">...</span>
        @endif
        <a href="{{ $paginator->url($lastPage) }}" class="page-btn">{{ $lastPage }}</a>
    @endif

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
    @else
        <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
    @endif

    {{-- Info --}}
    <span class="page-info">
        Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
    </span>
</div>
