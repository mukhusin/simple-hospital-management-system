@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label>Full Name</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ $user->name ?? '' }}" required>
					</div>
				</div>
				<div class="form-group">
					<label>Username</label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Username" name="username" value="{{ $user->username ?? '' }}" required>
					</div>
				</div>
			</div>

			<div class="form-inline" >
				<div class="form-group">
					<label>Password</label>
					<div>
						<input type="password" class="form-control" placeholder="Enter Password" name="password" required>
					</div>
				</div>
				
			</div>

			<?php

			$office_id = '';
			$role_id = '';

			if (isset($user->office_id)) {
				$office_id = $user->office_id;
			}
			if (isset($user->role_id)) {
				$role_id = $user->role_id;
			}

			?>

			<div class="form-inline" >
				<div class="form-group">
					<label>Office</label>
					<div>
						{{Form::select('office_id',
							Widget::offices(),
							$office_id,
							array('class' => 'form-control','required')
						)}}
					</div>
				</div>
				<div class="form-group">
					<label>Role</label>
					<div>
						{{Form::select('role_id',
							Widget::roles(),
							$role_id,
							array('class' => 'form-control','required')
						)}}
					</div>
				</div>
			</div>

			<!-- 
				<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			-->

			<div class="form-inline">
				<div class="form-group">
					<input type="submit" name="button" class="btn btn-success" value="Save Changes">
				</div>

				@if (isset($user->id))
					<div class="form-group">
						<a href="{{url('user/disabled/'.$user->id)}}" title="Delete User" class="verify btn btn-danger">Delete User</a>
					</div>
				@endif
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show