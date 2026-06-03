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
		<div class="col-lg-11">
			
			<!-- <h3>Reports</h3>			 -->
			
			<a href="{{url('report/income')}}" title="">
				<div class="box2">
					Daily Income Report
				</div>
			</a>

			<a href="{{url('report/inventory')}}" title="">
				<div class="box2">
					Inventory Report
				</div>
			</a>

			<a href="{{url('report/lab')}}" title="">
				<div class="box2">
					Labolatory Report
				</div>
			</a>

			<a href="{{url('report/attendance')}}" title="">
				<div class="box2">
					Attendance Report
				</div>
			</a>

			<a href="{{url('report/payments')}}" title="">
				<div class="box2">
					Payments Report
				</div>
			</a>
			
		</div>
	</div>
@endsection