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
                    <h3>Get Inventory Report
                        <hr>
                    </h3>
                    <form method="POST" action="{{ url()->current() }}">
@csrf
                    <div class="form-inline">

                        {{--<div class="form-group">--}}
                        {{--<label>Select Date</label>--}}
                        {{--<input type="date" name="date" class="form-control">--}}
                        {{--</div>--}}

                        <div class="form-group float1">
                            <label>
                                From </label> {{ Form::input( 'date','from', '' ,['class'=>'width_auto datepicker'] ) }}
                        </div>
                        <div class="form-group float1">
                            <label> To </label> {{ Form::input( 'date','to', '' ,['class'=>'width_auto datepicker'] ) }}
                        </div>

                        <div class="form-group float1">
                            <label> Type </label>
                            {{ Form::select( 'type', ['view'=>'view','download'=>'download'],'' ,['class'=>'width_auto '] ) }}
                        </div>

                        <hr width="100%">

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="button" value="Get a Report Now!">
                        </div>

                    </div>

                    </form>
                </div>
                <div class="col-lg-4">
                    <h3> 0 Inventory
                        <hr>
                    </h3>
                    <a target="_tab" href="{{url('report/inventoryzero')}}" class="btn btn-info btn-lg">Get "0
                        Inventory" Report</a>
                </div>

            </div>

            &nbsp;
            <hr>

            <div class="col-lg-12 well">
                <h3>Change Summary
                    <hr>
                </h3>

                <link rel="stylesheet" href="{{ asset('css/datatable.css') }}">
                <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
                <script type="text/javascript">
                    $(document).ready(function () {
                        oTable = $('#tabledata').dataTable({
                            // "bJQueryUI": true,
                            "sAjaxSource": "{{url('report/inventory/get')}}"
                            "aaSorting": [],
                            "bServerSide": true,
                            "sPaginationType": "full_numbers",
                            "bProcessing": true,
                        });
                    });
                </script>
                <table cellpadding="0" cellspacing="0" border="0" class="table display" id="tabledata">
                    <thead>
                        <tr>
                            <th>Date and Time</th>
                            <th>Medicine</th>
                            <th>Brand</th>
                            <th>Changes</th>
                            <th>Stock</th>
                            <th>Remark</th>
                            <th>Officer</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection