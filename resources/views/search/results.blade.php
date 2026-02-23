@extends('layout')
@section('title', 'Search – ' . ($query ? '"' . e($query) . '"' : 'All'))

@push('styles')
    <style>
        /* ── Search Results Page ── */
        .search-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 16px;
            padding: 36px 32px 28px;
            margin-bottom: 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .search-hero::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .search-hero h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .search-hero p {
            opacity: .85;
            margin: 0;
            font-size: .95rem;
        }

        /* Full-page search input */
        .search-form-wrapper {
            position: relative;
            max-width: 620px;
        }

        .search-form-wrapper .form-control {
            border-radius: 50px;
            padding: 14px 56px 14px 22px;
            font-size: 1rem;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .15);
        }

        .search-form-wrapper .btn-search {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50px;
            padding: 8px 22px;
            font-size: .9rem;
        }

        /* Section header */
        .section-label {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 12px;
        }

        /* Result cards */
        .result-card {
            border-radius: 12px;
            transition: transform .18s, box-shadow .18s;
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        /* File icon thumbnail */
        .file-thumb {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        /* Folder icon */
        .folder-icon-wrap {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* Empty state */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 72px;
            display: block;
            margin-bottom: 16px;
        }

        /* Highlight matched text */
        mark {
            background: rgba(99, 102, 241, .18);
            color: inherit;
            border-radius: 3px;
            padding: 0 2px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid pt-3">

        {{-- ── Hero / Search Bar ──────────────────────────────── --}}
        <div class="search-hero">
            <h2><i class="ri-search-line mr-2"></i>Search Your Drive</h2>
            @if($query)
                <p>Showing results for <strong>"{{ $query }}"</strong> &mdash;
                    {{ $files->count() + $folders->count() }} result(s) found
                </p>
            @else
                <p>Type a keyword to search through your files and folders.</p>
            @endif

            <div class="search-form-wrapper mt-4">
                <form action="{{ route('search.index') }}" method="GET" id="searchResultsForm">
                    <input type="text" name="q" id="searchResultsInput" class="form-control"
                        placeholder="Search for files or folders…" value="{{ e($query) }}" autocomplete="off" autofocus>
                    <button type="submit" class="btn btn-primary btn-search">
                        <i class="ri-search-line mr-1"></i> Search
                    </button>
                </form>
            </div>
        </div>

        @if($query)
            {{-- ── Folders ─────────────────────────────────────── --}}
            @if($folders->count() > 0)
                <div class="mb-4">
                    <div class="section-label">
                        <i class="ri-folder-fill mr-1"></i> Folders ({{ $folders->count() }})
                    </div>
                    <div class="row">
                        @foreach($folders as $folder)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('folders.show', $folder->id) }}" class="text-decoration-none">
                                    <div class="card result-card">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="folder-icon-wrap mr-3">
                                                <i class="ri-folder-fill"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="mb-1 text-truncate" title="{{ $folder->name }}">
                                                    {!! highlightQuery($folder->name, $query) !!}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="ri-calendar-line mr-1"></i>
                                                    {{ $folder->created_at->format('d M, Y') }}
                                                    &nbsp;·&nbsp;
                                                    {{ $folder->files->count() }} file(s)
                                                </small>
                                            </div>
                                            <i class="ri-arrow-right-s-line ml-auto text-muted" style="font-size:1.3rem;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Files ───────────────────────────────────────── --}}
            @if($files->count() > 0)
                <div class="mb-4">
                    <div class="section-label">
                        <i class="ri-file-line mr-1"></i> Files ({{ $files->count() }})
                    </div>
                    <div class="row">
                        @foreach($files as $file)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card result-card">
                                    <div class="card-body d-flex align-items-center">
                                        <img src="{{ $file->icon }}" class="file-thumb mr-3" alt="{{ $file->original_name }}">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-1 text-truncate" title="{{ $file->original_name }}">
                                                {!! highlightQuery($file->original_name, $query) !!}
                                            </h6>
                                            <small class="text-muted">
                                                {{ $file->formatted_size }}
                                                &nbsp;·&nbsp;
                                                {{ $file->created_at->format('d M, Y') }}
                                            </small>
                                        </div>
                                        <a href="{{ route('files.view', $file->id) }}" class="btn btn-sm btn-outline-primary ml-2"
                                            title="View file">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Nothing found ───────────────────────────────── --}}
            @if($files->count() === 0 && $folders->count() === 0)
                <div class="card">
                    <div class="card-body empty-state">
                        <i class="ri-search-eye-line"></i>
                        <h4 class="text-muted">No results for "{{ e($query) }}"</h4>
                        <p>Try a different keyword, or check the spelling.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-2">
                            <i class="ri-home-4-line mr-1"></i> Back to Home
                        </a>
                    </div>
                </div>
            @endif

        @else
            {{-- No query yet --}}
            <div class="card">
                <div class="card-body empty-state">
                    <i class="ri-search-line"></i>
                    <h4 class="text-muted">Enter a keyword above to search</h4>
                    <p>You can search for files and folders by name.</p>
                </div>
            </div>
        @endif

    </div>
@endsection

@php
    /**
     * Wrap matched text in <mark> for highlighting.
     */
    function highlightQuery(string $text, string $query): string
    {
        if (!$query)
            return e($text);
        $safe = e($text);
        $regex = '/' . preg_quote(e($query), '/') . '/iu';
        return preg_replace($regex, '<mark>$0</mark>', $safe);
    }
@endphp

@push('scripts')
    <script>
        // Allow pressing Enter in the search box to submit
        document.getElementById('searchResultsInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchResultsForm').submit();
            }
        });
    </script>
@endpush