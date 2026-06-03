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
			<div class="col-lg-12 well">

				{{ $text_string ?? '' }}

				{{ Form::open(['method'=>'get']) }}

                <div class="float1">
                    <label> From </label> {{ Form::input( 'date','from', $from ,['class'=>'width_auto datepicker'] ) }}
                </div>
                <div class="float1">
                    <label> To </label> {{ Form::input( 'date','to', $to ,['class'=>'width_auto datepicker'] ) }}
                </div>
                <div class="float1">
                    <button class="btn btn-warning" type="submit"> <i class="fa fa-search"></i> Get Report </button>
                </div>

                {{ Form::close(); }}


				<hr style="width:100%" />

				 <table class="table incomeTable tableExport" title="Income Report from {{date('d M Y',strtotime($from))}} to {{date('d M Y',strtotime($to))}} ">
                    <thead>
                        <tr>
                            <th> Income </th>
                            <th width="150" class="text-right"> Amount </th>
                            <th width="150" class="text-right"> Total Amount </th>
                        </tr>
                    </thead>
                    <tbody class="table-column">
                        <tr>
                            <td> Consultation </td>
                            <td class="text-right"> {{ number_format($cons)  }} </td>
                            <td>  </td>
                        </tr>
                        <tr>
                            <td> Service </td>
                            <td class="text-right"> {{ number_format($ser)  }} </td>
                            <td>  </td>
                        </tr>
                        <tr>
                            <td> Pharmacy </td>
                            <td class="text-right"> {{ number_format($phar)  }} </td>
                            <td>  </td>
                        </tr>
                        <tr>
                            <td> Laboratory </td>
                            <td class="text-right"> {{ number_format($lab)  }} </td>
                            <td>  </td>
                        </tr>

                        <tr>
                            <td> Forgiven </td>
                            <td class="text-right"> {{ number_format($for)  }} </td>
                            <td>  </td>
                        </tr>

                        <tr>
                            <td> Cash Sales ( Not included ) </td>
                            <td class="text-right"> {{ number_format($cash)  }} </td>
                            <td>  </td>
                        </tr>

                        <tr>
                            <td> Credit Sales ( Not included )  </td>
                            <td class="text-right"> {{ number_format($credit)  }} </td>
                            <td>  </td>
                        </tr>

                        <tr>
                            <td colspan="3"> &nbsp; </td>
                        </tr>

                        <tr>
                            <td> <strong> Total Income </strong> </td>
                            <td>  </td>
                            <td class="text-right"> <strong>{{ number_format($cash+$credit) }}</strong> </td>
                        </tr>

                        <tr>
                            <td colspan="3"> &nbsp; </td>
                        </tr>

                        <tr>
                            <td> <strong>Expenses</strong> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                        <?php
                         $ex = 0;
                        ?>

                        @foreach($expenses as $key => $expense)
                             <?php
                             $ex += $expense->total;
                            ?>
                            <tr>
                                <td> {{ $expense->name }} </td>
                                <td class="text-right"> {{ number_format($expense->total) }} </td>
                                <td class="text-right"> </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="3">  &nbsp; </td>
                        </tr>

                        <tr>
                            <td> <strong> Total Expenses </strong> </td>
                            <td>  </td>
                            <td class="text-right">  <strong>{{ number_format($ex)  }}</strong> </td>
                        </tr>

                        <tr>
                            <td colspan="3">  &nbsp; </td>
                        </tr>

                        <tr>
                            <td> <strong> Gross Profit </strong> </td>
                            <td>  </td>
                            <td class="text-right">  <strong>{{ number_format($cash + $credit - $ex) }}</strong> </td>
                        </tr>

                    </tbody>

                </table>

	            <script type="text/javascript" >



	            </script>

			</div>
    	</div>
	</div>
@endsection