@extends('layouts.app')
@section('title', __('messages.trial_details'))
@section('content')

<link rel="stylesheet" href="{{ asset('css/trial-show.css') }}">
<script src="{{ asset('js/sizeRecommendation.js') }}"></script>

<div class="trial-container">
    <div class="header">
        <h1>{{ __('messages.trial_details') }}</h1>
    </div>

    <div class="trial-name-section">
        @if($trial->image_data)
        <div class="trial-image-container">
            <img
                src="{{ $trial->image_data }}"
                alt="{{ $trial->trial_name }}"
                class="trial-image"
                loading="lazy">
        </div>
        @endif

        <div class="trial-info">
            <div class="trial-label">
                {{ __('messages.trial_name') }}
            </div>
            <div class="trial-value">
                {{ $trial->trial_name }}
            </div>
        </div>
    </div>
    <div class="measurements-section">
    <div class="measurements-container">
        <div class="measurement-info" data-tip="Chest measurement from side to side">
            <div class="info-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M3 12h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Chest Width</div>
                <div class="info-value">{{ round($chestWidth) ?? '0' }}</div>
            </div>
        </div>

        <div class="measurement-info" data-tip="Shoulder width from point to point">
            <div class="info-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 12h12"></path>
                    <path d="M3 6l3 6-3 6"></path>
                    <path d="M21 6l-3 6 3 6"></path>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Shoulder Width</div>
                <div class="info-value">{{ round($shoulderWidth, 2) ?? '0' }}</div>
            </div>
        </div>

        <div class="measurement-info" data-tip="Sleeve length from shoulder to wrist">
            <div class="info-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 6v12"></path>
                    <path d="M6 12h12"></path>
                </svg>
            </div>
            <div class="info-content">
                <div class="info-label">Sleeve Length</div>
                @php
                $measurements = json_decode($measurements, true);
                @endphp
                <div class="info-value">
                    {{ isset($measurements['right_sleeve_cm']) ? number_format($measurements['right_sleeve_cm'], 1) : '0' }}
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="size-recommendations">
        <div class="recommendations-header">
            <h2>{{ __('messages.size_recommendations') }}</h2>
        </div>

        <div class="shoulder-selector">
            <div class="shoulder-selector-content">
                <div class="shoulder-visualization">
                    <div class="shoulder-guide">
                        <div class="shoulder-guide-line">
                            <img src="{{ asset('images/shoulder.png') }}" alt="Shoulder Guide">
                        </div>
                        <div class="measurement-value">
                            <span id="shoulder-width-value">{{ isset($shoulderWidth) ? round($shoulderWidth, 2) : 0 }}</span> cm
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="size-filters">
            <div class="filter-group">
                <label class="filter-label">{{ __('messages.material') }}</label>
                <select class="filter-select" id="material-filter">
                    <option value="all">{{ __('messages.all_materials') }}</option>
                    <option value="Cotton">{{ __('messages.cotton') }}</option>
                    <option value="Polyester">{{ __('messages.polyester') }}</option>
                    <option value="Blended">{{ __('messages.blended') }}</option>
                    <option value="Stretchable">{{ __('messages.stretchable') }}</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">{{ __('messages.style') }}</label>
                <select class="filter-select" id="style-filter">
                    <option value="all">{{ __('messages.all_styles') }}</option>
                    <option value="regular">{{ __('messages.regular') }}</option>
                    <option value="slim_fit">{{ __('messages.slim_fit') }}</option>
                    <option value="oversize">{{ __('messages.oversize') }}</option>
                </select>
            </div>
        </div>

        <div class="size-cards">
            @foreach(json_decode($sizes) as $recommendation)
            <div class="size-card" data-material="{{ $recommendation->material }}" data-style="{{ $recommendation->style }}">
                <div class="size-indicator">{{ $recommendation->size }}</div>
                <div class="size-details">
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.material') }}</span>
                        <span>{{ $recommendation->material }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">{{ __('messages.style') }}</span>
                        <span>{{ ucwords(str_replace('_', ' ', $recommendation->style)) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <a href="{{ route('trial.create') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_form') }}
    </a>

    @endsection