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
					    {{Form::select('expense_id',
                            Expense::all()->lists('name','id'),
                            '',
                            array('class' => 'form-control','required')
                        )}}
					</div>
				</div>

				<div class="form-group">
                    <label> Remark </label>
                    <div>
                        <input type="text" class="form-control" placeholder="Enter Remark" name="remark" value="{{ $expense->remark ?? '' }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label> Amount </label>
                    <div>
                        <input type="number" class="form-control" placeholder="Enter Amount" name="amount" value="{{ $expense->amount ?? '' }}" required>
                    </div>
                </div>

			</div>

			<input type="submit" class="btn btn-success" name="button" value="Save Changes">
			<!-- <hr> -->
		</div>
	</div>
	</form>
@show