@extends('master')

@section('main')
	<form method="POST" action="{{ url()->current() }}">
@csrf
	<div class="row">
		<div class="col-lg-12">
			<div class="form-inline" >
				<div class="form-group">
					<label> Expense Ledger </label>
					<div>
						<input type="text" class="form-control" placeholder="Enter Ledger Name" name="name" value="{{ $expense->name ?? '' }}" required>
					</div>
				</div>

			</div>

			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show