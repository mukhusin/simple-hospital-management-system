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
    <?php

    $Mon = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");


    ?>
    <div class="col-lg-12 well">
        <h3>Number of Patients Attendanded</h3>
             <div class="graph">
                <div id="tp"></div>
            </div>
            <script type="text/javascript">
                Morris.Bar({
                element: 'tp',
                data: [
                    @foreach ($total as $k => $v)
                        <?php
                        $i = $j = 0;
                        if (isset($v[0])) {
                            $i = $v[0];
                        }
                        if (isset($v[1])) {
                            $j = $v[1];
                        }
                        ?>
                        {x: '{{$k}}', y: {{$i}}, z: {{$j}}},
                    @endforeach
                ],
                xkey: 'x',
                ykeys: ['y', 'z'],
                labels: ['New Attendanded','Re Attendanded']
                }).on('click', function(i, row){
                    console.log(i, row);
                });
            </script>
    </div>



@endsection