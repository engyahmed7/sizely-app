@extends('layouts.app')
@section('title', __('messages.create_trial'))
@section('content')

<link rel="stylesheet" href="{{ asset('css/pose-detection.css') }}">

<div class="container">
    @if(session('error'))
    <div class="alert alert-warning">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.alert').style.display = 'none';
        }, 2000);
    </script>
    @endif
    <h1>{{ __('messages.body_measurement_form') }}</h1>

    <form id="trialForm" enctype="multipart/form-data">
        @csrf

        <div id="step1">
            <div class="form-group">
                <label for="trial_name">{{ __('messages.trial_name') }}</label>
                <input type="text" id="trial_name" name="trial_name" required
                    placeholder="Enter trial name...">
            </div>
            <button type="button" id="nextStep" class="btn">
                {{ __('messages.next_step') }} <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <div id="face_step" style="display: none;">
            <div class="data-preview" id="face_data_preview">
                <video id="video_face" autoplay playsinline></video>
                <canvas id="output_face"></canvas>
            </div>

        </div>

        <div id="step2" style="display: none;">
            <div id="loading" style="display: none;">
                <div class="spinner-text"> Loading PoseNet model... </div>
                <div class="sk-spinner sk-spinner-pulse"></div>
            </div>
            <div id="main" style="display: block;"> <video id="video" playsinline="" style="-moz-transform:scaleX(-1);-o-transform:scaleX(-1);-webkit-transform:scaleX(-1);transform:scaleX(-1);display:none;" width="600" height="500"> </video> <canvas id="output" width="600" height="500"> </canvas>
                <div style="position: fixed; top: 0px; left: 0px; cursor: pointer; opacity: 0.9; z-index: 10000;"><canvas width="80" height="48" style="width: 80px; height: 48px; display: block;"></canvas><canvas width="80" height="48" style="width: 80px; height: 48px; display: none;"></canvas><canvas width="80" height="48" style="width: 80px; height: 48px; display: none;"></canvas></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.7.6/dat.gui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/stats.js/r16/Stats.min.js"></script>
<script type="module" src="{{ asset('dist/bundle.js') }}"></script>

@endsection