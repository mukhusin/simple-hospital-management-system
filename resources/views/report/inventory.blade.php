@extends('master')

@section('main')
    <div class="row">
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
        <div class="col-lg-12">

            <div class="col-lg-12 well	">
                <div class="col-lg-8">
                    <form method="POST" action="{{ url()->current() }}">
@csrf
                    <div class="form-inline">

                        <div class="form-group float1">
                            <label> From </label> {{ Form::input( 'date','from', '' ,['class'=>'width_auto datepicker'] ) }}
                        </div>
                        <div class="form-group float1">
                            <label> To </label> {{ Form::input( 'date','to', '' ,['class'=>'width_auto datepicker'] ) }}
                        </div>

                        <hr width="100%">

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="button" value="Get a Report Now!">
                        </div>

                    </div>

                    </form>
                </div>

            </div>

        </div>
    </div>
@endsection