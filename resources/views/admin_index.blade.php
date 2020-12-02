@extends('layouts.admin')

@section('content')
    <script src="{{ asset('js/admin/chart-2.9.4.js') }}"></script>
    <section class="review-chart">
        <input type="hidden" id="data" value="{{ $data }}">
        <input type="hidden" id="title" value="{{ trans('msg.new_review_per_month') }}">
        <input type="hidden" id="label" value="{{ trans('msg.new_review') }}">
        <canvas id="line-chart"></canvas>
    </section>
    <script src="{{ asset('js/admin/chart.js') }}"></script>
@endsection
