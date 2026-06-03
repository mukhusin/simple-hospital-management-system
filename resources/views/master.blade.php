<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title> DAVIDSON EMMANUEL SPECIALIZED PAEDIATRIC CENTRE </title>
    <meta name="generator" content="Bootply"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('css/default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/default.js') }}"></script>
    <style type="text/css" media="screen">
        .form-control {
            padding: 5px;
            margin: 2px 0px;
        }
    </style>
    <script type="text/javascript">
        function ReturnFalse() {
            return false;
        }
    </script>
    @stack('styles')

</head>
<body>
<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-toggle"></span>
            </button>
            <a class="navbar-brand" href="{{url('dashboard')}}">
                <img src="{{url('images/logo.png')}}" height="20" alt="">
                DAVIDSON EMMANUEL SPECIALIZED PAEDIATRIC CENTRE
            </a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">
                        <i class="icon-user-md"></i> {{Auth::user()->name}}
                        <span class="caret"></span>
                    </a>
                    <ul id="g-account-menu" class="dropdown-menu" role="menu">
                        <li><a href="{{url('settings')}}">settings</a></li>
                        <li><a href="{{url('logout')}}">logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /container -->
</div>

<!-- <div class="container"> -->

<?php

$title = $title ?? '';
$sub_title = $sub_title ?? '';
$page_index = $page_index ?? '';

$menu = array(
    '1' => array(
        array('add_patient',
            'patient/edit/0',
            'Add Patient',
            '<i class="icon-plus-sign"></i>'),
        array('patient',
            'patients',
            'Patient',
            '<i class="icon-user"></i>'),
        array('movement',
            'movements',
            'Patient Movement',
            '<i class="icon-exchange"></i>'),
        array('history',
            'history',
            'History',
            '<i class="icon-level-down"></i>'),
        array('report',
            'report',
            'Report',
            '<i class="icon-tasks"></i>',
            array(
                array('Attendance', 'attendances'),
                array('Payments', 'payments'),
                array('My Daily Report', 'report1'),
            ),
        ),
    ),
    '2' => array(
        array('patient',
            'patients',
            'Patient',
            '<i class="icon-user"></i>'),
        array('history',
            'history',
            'History',
            '<i class="icon-level-down"></i>'),
    ),
    '3' => array(
        array('patient',
            'patients',
            'Patient',
            '<i class="icon-user"></i>'),
        array('inpatient',
            'inpatients',
            'In Patient',
            '<i class="icon-bullseye"></i>'),
        array('peformance',
            'doctor/peformance',
            'Performance Report',
            '<i class="icon-user-md"></i>'),
        array('modify',
            'doctor/modify',
            'Modify',
            '<i class="icon-edit"></i>'),
        array('emergence',
            'doctor/emergence',
            'Emergence Treatment',
            '<i class="icon-edit"></i>'),
        array('history',
            'history',
            'History',
            '<i class="icon-level-down"></i>'),
        array('diagnosis',
            'doc/diagnosis',
            'Diagnosis',
            '<i class="icon-asterisk"></i>'),
    ),
    '4' => array(
        array('patient',
            'patients',
            'Patient',
            '<i class="icon-user"></i>'),
        array('lab_edit',
            'lab/edit',
            'Modify Results',
            '<i class="icon-edit"></i>'),
        array('history',
            'history',
            'History',
            '<i class="icon-level-down"></i>'),
    ),
    '5' => array(
        array('patient',
            'patients',
            'Patient',
            '<i class="icon-user"></i>'),
        array('emergence',
            'emergence/dispense',
            'Emergence Request',
            '<i class="icon-user"></i>'),
        array('medicine',
            'pharmacy_medicines',
            'Medicines',
            '<i class="icon-user-md"></i>'),
        array('history',
            'history',
            'History',
            '<i class="icon-level-down"></i>'),
        array('report',
            'pharmacy/report',
            'Report',
            '<i class="icon-bullseye"></i>'),
    ),
    '6' => array(
        array('patient',
            'patients',
            'Patients',
            '<i class="icon-user"></i>'),
        array('lab',
            'lab',
            'Lab Test',
            '<i class="icon-adjust"></i>'),
        array('medicine',
            'medicines',
            'Medicines',
            '<i class="icon-user-md"></i>'),
        array('diagnosis',
            'diagnosis',
            'Diagnosis',
            '<i class="icon-asterisk"></i>'),
        array('bills',
            'bills',
            'Patient Bills',
            '<i class="icon-money"></i>'),

        array('service',
            'service',
            'Services',
            '<i class="icon-double-angle-down"></i>',
            array(
                ['Insurance', 'insurance'],
                ['Procedures', 'procedure'],
                ['Wards (For In-Patient)', 'wards'],
            ),
        ),

        array('financial',
            'financial',
            'Financial',
            '<i class="icon-double-angle-down"></i>',
            array(
                ['Expense ledger', 'expense_ledger'],
                ['Expense Transaction', 'expense_transaction'],
                //['logs','logs'],
                ['Invoice', 'invoice'],
            ),
        ),

        array('report',
            'report',
            'Reports',
            '<i class="icon-double-angle-down"></i>',
            array(
                ['Sales Summary', 'sales_report'],
                ['Expense Report', 'expense_report'],
                ['Profit Analysis', 'income_statement'],
                ['Medicine Inventory', 'inventory'],
                ['Laboratory', 'lab'],
                ['Attendance', 'attendances'],
                ['Payments', 'payments'],
                ['Inventory Status', 'inventorystatus'],
            ),
        ),
        array('followup',
            'followup',
            'Patient Followups',
            '<i class="icon-reply"></i>'),
        array('user',
            'users',
            'User Ctrl',
            '<i class="icon-user"></i>'),
    ),
);

?>


<div class="row1">
    <div id="side" class="col-sm-3">
        <div class="nav-side-menu">
            <div class="brand"> HosCare Menu</div>
            <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
            <div class="menu-list">

                <ul id="menu-content" class="menu-content collapse out">

                    @if ($page_index == 'dashboard')
                        <li class="active">
                            <a href="{{url('dashboard')}}"><i class="icon-home"></i> Dashboard</a>
                        </li>
                    @else
                        <li>
                            <a href="{{url('dashboard')}}"><i class="icon-home"></i> Dashboard</a>
                        </li>
                    @endif

                    <?php $m1 = $menu[Auth::user()->role_id]; ?>
                    @foreach ($m1 as $j => $v)
                        @if ($page_index == $v[0] && isset($v[4]) && is_array($v[4]))
                            <li data-toggle="collapse" data-target="#new1{{$j}}" class="active">
                                <a href="#">
                                    <div>{!! $v[3] !!} {{$v[2]}}
                                        <span class="pull-right">
                                        <i class="icon"></i>
                                    </span>
                                    </div>
                                </a>
                                <ul class="sub-menu collapse" id="new1{{$j}}">
                                    @foreach ($v[4] as $e)
                                        <li>
                                            <a href="{{url($v[1].'/'.$e[1])}}">
                                                {{$e[0]}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <script type="text/javascript">
                                $(function () {
                                    $("#new1{{$j}}").collapse();
                                });
                            </script>

                        @elseif ($page_index == $v[0])
                            <li class="active">
                                <a href="{{url($v[1])}}"> {!! $v[3] !!} {{$v[2]}}</a>
                            </li>
                        @elseif (isset($v[4]) && is_array($v[4]))
                            <li data-toggle="collapse" data-target="#new2{{$j}}" class="collapsed">
                                <a href="#">
                                    {!! $v[3] !!} {{$v[2]}}
                                    <span class="pull-right"><i class="icon"></i></span>
                                </a>
                                <ul class="sub-menu collapse" id="new2{{$j}}">
                                    @foreach ($v[4] as $e)
                                        <li>
                                            <a href="{{url($v[1].'/'.$e[1])}}">
                                                {{$e[0]}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li>
                                <a href="{{url($v[1])}}"> {!! $v[3] !!} {{$v[2]}}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <div id="cont" class="col-sm-9 container">
        <h3>
            {!! $sub_title !!} {{ $title }}
            <span class="pull-right">
                        <a href="javascript:history.go(-1)" class="refresh" title="Go back">
                            <i class="icon-arrow-left"></i>
                        </a>
                        |
                        <a href="#" class="refresh" title="Refresh" onclick="window.location.reload(true);">
                            <i class="icon-refresh"></i>
                        </a>
                        &nbsp;
                        <a href="#" class="refresh" title="Refresh" onclick="$('.vhidden').show();">
                            .
                        </a>
                    </span>
        </h3>
        <hr>
        @yield('main')

        <div class="row">
            <hr>
        </div>

        <!-- <hr>
        HosCare -->
    </div>

</div>


<script src="{{ asset('js/bootstrap.min.js') }}"></script>

<script type="text/javascript">
    $('.text_editor').jqte();
    // settings of status
    var jqteStatus = true;
    $(".status").click(function () {
        jqteStatus = jqteStatus ? false : true;
        $('.text_editor').jqte({"status": jqteStatus})
    });

    var config = {
        '.chosen-select': {},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "99%"}
    }
    for (var selector in config) {
        $(selector).chosen(config[selector]);
    }

    $(document).ready(function () {

        $('.vhidden').hide();
        $('input[type=date]').each(function () {
            var $input = $(this);
            $input.datepicker({
                // minDate: $input.attr('min'),
                // maxDate: $input.attr('max'),
                dateFormat: 'yy-mm-dd'
            });
        });

        $('input[type=time]').each(function () {
            var $input = $(this);
            $input.timepicker({});
        });

    });

    $('.verify').live('click', function (event) {
        var answer = confirm("Are you sure ? ")
        if (answer) {
            return true;
        }
        else {
            return false;
        }
    });

</script>

@stack('scripts')

</body>

</html>