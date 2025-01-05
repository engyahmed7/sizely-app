@extends('layouts.app')
@section('title', __('messages.trials_list'))

@section('content')
<link rel="stylesheet" href="{{ asset('css/trial-index.css') }}">

<div class="dashboard">
    <div class="content-wrapper">
        <div class="header">
            <h1>{{ __('messages.trials_list') }}</h1>
        </div>

        <div class="table-container">
            @if($trials->isEmpty())
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>{{ __('messages.no_trials_found') }}</p>
            </div>
            @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.trial_name') }}</th>
                            <th>{{ __('messages.shoulder_width_cm') }}</th>
                            <th>{{ __('messages.right_eye') }}</th>
                            <th>{{ __('messages.left_eye') }}</th>
                            <th>{{ __('messages.right_shoulder') }}</th>
                            <th>{{ __('messages.left_shoulder') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trials as $trial)
                        <tr>
                            <td>
                                <a href="{{ route('trial.show', ['trial' => $trial->id]) }}">
                                    {{ $trial->trial_name }}
                                </a>
                            </td>
                            <td>{{ round($trial->shoulder_cm, 2) }}</td>
                            <td>
                                X: {{ $trial->righteye['x'] ?? '-' }}
                                </BR>
                                Y: {{ $trial->righteye['y'] ?? '-' }}
                            </td>
                            <td>
                                X: {{ $trial->lefteye['x'] ?? '-' }}
                                </BR>
                                Y: {{ $trial->lefteye['y'] ?? '-' }}
                            </td>
                            <td>
                                X: {{ $trial->rightshoulder['x'] ?? '-' }}
                                </BR>
                                Y: {{ $trial->rightshoulder['y'] ?? '-' }}
                            </td>
                            <td>
                                X: {{ $trial->leftshoulder['x'] ?? '-' }}
                                </BR>
                                Y: {{ $trial->leftshoulder['y'] ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $trials->links('vendor.pagination.custom') }}
            </div>
            @endif
        </div>
    </div>
</div>