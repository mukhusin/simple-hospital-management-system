@extends('master')

@section('main')
<div class="row">

	<div class="col-lg-8">

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

     	<form method="POST" action="{{ url()->current() }}">
@csrf
			<div class="form-inline" >
				<label for="inputName">Name</label>
				<input type="text" class="form-control" placeholder="Enter User fullname" name="name" value="{{Auth::user()->name}}" readonly>
			</div>

			<div class="form-inline" >
				<label for="inputUsername">Username</label>
				<input type="text" class="form-control" placeholder="Enter Username for user" name="username" value="{{Auth::user()->username}}" readonly>
			</div>
			
			<div class="form-inline" >
				<label for="inputName">Password</label>
				<input type="password" class="form-control" placeholder="Enter New Password" name="password_1" required>
			</div>

			<div class="form-inline" >
				<label for="inputName">Retype Password</label>
				<input type="password" class="form-control" placeholder="Retype Password" name="password_2" required>
			</div>

			<button type="submit" class="btn btn-primary">Save changes</button>
	</div>
</div>
@endsection