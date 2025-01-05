@extends('layouts.app')
@section('title', __('messages.create_trial'))
@section('content')

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/posenet"></script>
<link rel="stylesheet" href="{{ asset('css/pose-detection.css') }}">

<div class="container">
    <h1>{{ __('messages.body_measurement_form') }}</h1>

    <form id="trialForm" enctype="multipart/form-data">
        @csrf
        <div id="step1">
            <div class="form-group">
                <label for="trial_name">{{ __('messages.trial_name') }}</label>
                <input type="text" id="trial_name" name="trial_name" required
                    placeholder="Enter trial name...">
                <label for="gender">{{ __('messages.gender') }}</label>
                <div class="gender-select">
                    <div class="gender-option">
                        <input type="radio" id="male" name="gender" value="male" required>
                        <label class="gender-card" for="male">
                            <div class="gender-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2a2 2 0 0 1 2 2 2 2 0 0 1-2 2 2 2 0 0 1-2-2c0-1.1.9-2 2-2zm8 7h-5v13h-2v-6h-2v6H9V9H4V7h16v2z" />
                                </svg>
                            </div>
                            <div class="gender-label">Male</div>
                        </label>
                    </div>

                    <div class="gender-option">
                        <input type="radio" id="female" name="gender" value="female" required>
                        <label class="gender-card" for="female">
                            <div class="gender-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2a2 2 0 0 1 2 2 2 2 0 0 1-2 2 2 2 0 0 1-2-2c0-1.1.9-2 2-2zm-1 19v-6H8V8h8v7h-3v6h-2z" />
                                </svg>
                            </div>
                            <div class="gender-label">Female</div>
                        </label>
                    </div>
                </div>
            </div>
            <button type="button" id="nextStep" class="btn">
                {{ __('messages.next_step') }} <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <div id="step2" style="display: none;">
            <div class="data-preview">
                <video id="video" autoplay playsinline></video>
                <canvas id="output"></canvas>
            </div>

        </div>
    </form>

    <div class="success-message">
        {{ __('messages.trial_saved') }} <i class="fas fa-check-circle"></i>
    </div>
</div>

<script>
    const messages = @json(__('messages.pose_detection'));
</script>

<script type="module" src="{{ asset('js/trial.js') }}"></script>

@endsection