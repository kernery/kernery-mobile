@php
    PageTitle::setTitle(trans('service_unavailable'));
@endphp

@extends('core/main::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">{{trans('503_code')}}</div>
        <p class="empty-title">{{ trans('service_unavailable') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ trans('503_message') }}
        </p>
    </div>
@endsection
