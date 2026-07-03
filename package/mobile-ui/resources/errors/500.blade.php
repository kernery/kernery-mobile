@php
    PageTitle::setTitle(trans('internal_server_error'));
@endphp

@extends('core/main::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">{{trans('500_code')}}</div>
        <p class="empty-title">{{ trans('internal_server_error') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ trans('500_message') }}
        </p>
    </div>
@endsection
