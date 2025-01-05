@extends('layouts.app')
@section('title', __('messages.trial_details'))
@section('content')

<link rel="stylesheet" href="{{ asset('css/trial-show.css') }}">

<div class="trial-container">
    <div class="header">
        <h1>{{ __('messages.trial_details') }}</h1>
    </div>

    <div class="trial-name">
        @if($trial->image_data)
        <img src="{{ $trial->image_data }}" alt="Trial Image">
        @endif
        <br />
        <strong>{{ __('messages.trial_name') }}:</strong> {{ $trial->trial_name }}
    </div>

    <div class="user-info-container">
        <div class="info-card shoulder-info">
            <div class="info-icon">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 4h-4l2 4h-3l-2-4H7L5 8h3l2 4H7l-2-4H2" />
                    <path d="M22 8h-3l-2-4h-4" />
                </svg>
            </div>
            <div class="info-content">
                <span class="info-label">{{ __('messages.shoulder_width_cm') }}</span>
                <span class="info-value">{{ round($trial->shoulder_cm, 2) }} cm</span>
            </div>
        </div>

        <div class="info-card gender-info">
            <div class="info-icon">
                @if ($trial->gender === 'male')
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="6" r="3" />
                    <path d="M12 9v15M8 16h8" />
                </svg>
                @else
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="3" />
                    <path d="M12 11v9M9 17l3 3 3-3" />
                </svg>
                @endif
            </div>
            <div class="info-content">
                <span class="info-label">{{ __('messages.gender') }}</span>
                <span class="info-value">{{ ucfirst($trial->gender) }}</span>
            </div>
        </div>
    </div>

    <h2>{{ __('messages.measurements') }}</h2>
    <div class="measurements-grid">
        <div class="measurement-card">
            <div class="measurement-title">
                <i class="fas fa-eye"></i> {{ __('messages.right_eye') }}
            </div>
            <div class="measurement-value">
                <pre>{{ json_encode($trial->righteye, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="measurement-card">
            <div class="measurement-title">
                <i class="fas fa-eye"></i> {{ __('messages.left_eye') }}
            </div>
            <div class="measurement-value">
                <pre>{{ json_encode($trial->lefteye, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="measurement-card">
            <div class="measurement-title">
                <i class="fas fa-male"></i> {{ __('messages.right_shoulder') }}
            </div>
            <div class="measurement-value">
                <pre>{{ json_encode($trial->rightshoulder, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="measurement-card">
            <div class="measurement-title">
                <i class="fas fa-male"></i> {{ __('messages.left_shoulder') }}
            </div>
            <div class="measurement-value">
                <pre>{{ json_encode($trial->leftshoulder, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>

    <a href="{{ route('trial.create') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_form') }}
    </a>
</div>

@endsection