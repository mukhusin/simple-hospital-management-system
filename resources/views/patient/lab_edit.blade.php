@extends('master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
@endpush

@section('main')

    <div class="col-lg-12">
        @if (session('error'))
            <div class="error-div">
                {{session('error')}}
            </div>
        @endif

        @if (session('success'))
            <div class="success-div">
                {{session('success')}}
            </div>
        @endif
    </div>
        
    <div class="col-lg-12 widget1">
        <sub_title>Edit Lab Test Results</sub_title>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabledata2">
            <thead>
                <tr>
                    <th>Test Queue</th>
                    <th>Labolatory Test</th>
                    <th>Sample</th>
                    <th>Requestee</th>
                    <th>Patient</th>
                    <th>Gender</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr>
                        <td>{{$d->time}}</td>
                        <td>{{$d->test}}</td>
                        <td>{{$d->al->sample_register}}</td>
                        <td>{{$d->from_user}}</td>
                        <td>{{$d->patient}}</td>
                        <td>{{$d->gender}}</td>
                        <td>
                            <a class="opt" href="{{url('result/'.$d->al->id)}}"> + result </a>
                        </td>
                    </tr>
                @endforeach         
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#tabledata2').DataTable({ order: [], pageLength: 25 });
});
</script>
@endpush