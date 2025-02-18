@extends('layouts.app')
@section('title', __('messages.trials_list'))

@section('content')
<link rel="stylesheet" href="{{ asset('css/trial-index.css') }}">
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
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
                            <th>{{ __('messages.chest_width_cm') }}</th>
                            <th>{{ __('messages.actions') }}</th>
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
                            <td>{{ round($trial->chest_cm, 2) }}</td>
                            <td>
                                <div class="actions-container">
                                    <a href="{{ route('trial.show', ['trial' => $trial->id]) }}" class="action-button view-button">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="action-button delete-button" onclick="showDeleteModal({{$trial->id}})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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

<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="deleteModal">
    <div class="modal-header">
        <h3 class="modal-title">{{ __('messages.confirm_deletion') }}</h3>
    </div>
    <div class="modal-body">
        {{ __('messages.delete_confirmation_message') }}
    </div>
    <div class="modal-footer">
        <button type="button" class="modal-button cancel-button" onclick="hideDeleteModal()">
            {{ __('messages.cancel') }}
        </button>
        <form id="deleteForm" method="POST" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="modal-button confirm-button">
                {{ __('messages.confirm') }}
            </button>
        </form>
    </div>
</div>

<script src="{{ asset('js/trial-index.js') }}"></script>