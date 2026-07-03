@php
    PageTitle::setTitle(trans('page_not_found'));
@endphp

@extends('core/main::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">{{ trans('404_code') }}</div>
        <p class="empty-title">{{ trans('page_not_found') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ trans('404_message') }}
        </p>
    </div>
@endsection
