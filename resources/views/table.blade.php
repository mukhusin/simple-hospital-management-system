@extends('master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
@endpush

@section('main')
    <div class="row">
        <div class="col-lg-12">
            @if (session('error'))
                <div class="error-div">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="success-div">{{ session('success') }}</div>
            @endif
        </div>

        <div class="col-lg-12">
            {{ $text_string ?? '' }}

            <table id="tabledata" class="display table" cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr>
                        @foreach ($table->columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    @php
        $isServer = isset($table->url) && ($table->type ?? '') === 'server';
        $hasKeys  = isset($table->column_keys) && count($table->column_keys);
    @endphp

    window.oTable = $('#tabledata').DataTable({
        processing: true,
        serverSide: {{ $isServer ? 'true' : 'false' }},
        @if ($isServer)
        ajax: '{{ url($table->url) }}',
        @endif
        @if ($hasKeys)
        columns: [
            @foreach ($table->column_keys as $key)
                { data: '{{ $key }}' },
            @endforeach
        ],
        @endif
        pageLength: 25,
        order: [],
        language: {
            processing: '<i class="icon-refresh"></i> Loading...',
        },
    });
});
</script>
@endpush