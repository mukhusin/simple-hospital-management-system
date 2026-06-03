<style type="text/css" media="screen">
	a.btn{
		text-decoration:none;
		padding:10px 20px;
		border:1px solid whitesmoke;
		color:gray;
	}
	font.btn1 {
	    color: black;
	}
</style>
<h3>DAVIDSON EMMANUEL SPECIALIZED PAEDIATRIC CENTRE</h3>
<table>
	<tr>
		<td><img width="100" src="{{url('images/logo.png')}}" alt="logo"></td>
		<td>
			<table cellpadding="2" cellspacing="2" border="0">
				<tr>
					<td style="font-size:11px ">Tel</td>
					<td style="font-size:11px ">+255 222 460 014</td>
				</tr>
				<tr>
					<td style="font-size:11px" >Mob</td>
					<td style="font-size:11px" >+255 782 244 010</td>
				</tr>
				<tr>
					<td style="font-size:11px" ></td>
					<td style="font-size:11px" >+255 766 244 010</td>
				</tr>
				<tr>
					<td style="font-size:11px" ></td>
					<td style="font-size:11px" >+255 712 183 843</td>
				</tr>
				<tr>
					<td style="font-size:11px" >Email</td>
					<td style="font-size:11px" >davison.specialized@gmail.com</td>
				</tr>
				<tr>
					<td style="font-size:11px" colspan="2">P.O Box 7760 Dar-es-Salaam, Sinza Kijiweni</td>
				</tr>
			</table>
		</td>
	</tr>
</table>	

<hr>

<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td align="left"> Staff Name : {{Auth::user()->name}} </td>
		<td align="left"> Report Date : {{date('D, d M Y',strtotime(date($date)))}} </td>
		<td align="right"> Report Generate at : {{date('H:i, d M Y',strtotime(date('Y-m-d H:i:s')))}} </td>
	</tr>
</table>

@if (isset($data['full']))
	<table width="100%" cellpadding="5" cellspacing="0" border="1">

		<tr><td colspan="5">Full served partients</td></tr>
		<?php
		$bill1 = $paid1 = 0;
		?>
		<tr>
			<td>Patient</td>
			<td>Gender</td>
			<td>Payment Type</td>
			<td align="right">Bill</td>
			<td align="right">Paid</td>
		</tr>
		@foreach ($data['full'] as $v)
			<tr>
				<td>{{$v['patient']}}</td>
				<td>{{$v['gender']}}</td>
				<td>{{$v['type']}}</td>
				<td align="right">{{number_format($v['bill'])}}</td>
				<td align="right">{{number_format($v['paid'])}}</td>
			</tr>
			<?php
			$bill1 += $v['bill'];
			$paid1 += $v['paid'];
			?>
		@endforeach
			<tr>
				<td colspan="3"> Total Balance </td>
				<td align="right">{{number_format($bill1)}}</td>
				<td align="right">{{number_format($paid1)}}</td>
			</tr>
	</table>
@endif

@if (isset($data['end']))
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr><td colspan="7">Handover Patients</td></tr>
		<tr>
			<td>Patient</td>
			<td>Gender</td>
			<td>Payment Type</td>
			<td>Previous Collector</td>
			<td>Amount collected</td>
			<td align="right">Bill</td>
			<td align="right">Paid</td>
		</tr>
		<?php
		$bill2 = $paid2 = $other = 0;
		?>
		@foreach ($data['end'] as $v)
			<tr>
				<td>{{$v['patient']}}</td>
				<td>{{$v['gender']}}</td>
				<td>{{$v['type']}}</td>
				<td>{{$v['other_name']}}</td>
				<td align="right">{{$v['other']}}</td>
				<td align="right">{{$v['bill']}}</td>
				<td align="right">{{$v['paid']}}</td>
			</tr>
			<?php
			$bill2 += $v['bill'];
			$paid2 += $v['paid'];
			$other += $v['other'];
			?>
		@endforeach
			<tr>
				<td colspan="4"> Total Balance </td>
				<td align="right">{{number_format($other)}}</td>
				<td align="right">{{number_format($bill2)}}</td>
				<td align="right">{{number_format($paid2)}}</td>
			</tr>
	</table>
@endif

@if (isset($data['pre']))
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr><td colspan="4">Collected Consultation Fee </td></tr>
		<?php
		$sub = 0;
		?>
		<tr>
			<td>Patient</td>
			<td>Gender</td>
			<td align="right">Bill</td>
		</tr>
		@foreach ($data['pre'] as $v)
			<tr>
				<td>{{$v['patient']}}</td>
				<td>{{$v['gender']}}</td>
				<td align="right">{{number_format($v['other'])}}</td>
			</tr>
			<?php
			$sub += $v['other'];
			?>
		@endforeach
			<tr>
				<td colspan="2"> Total Balance </td>
				<td align="right">{{number_format($sub)}}</td>
			</tr>
	</table>
@endif

<hr>

<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td align="left"> {{Auth::user()->name}} Signature : ___________________</td>
		<td align="left"> Manager Signature : ___________________</td>
	</tr>
</table>

<hr>

<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td align="left" style="font-size:11px">
			HosCare v1.0 <br> CapsLock Ltd <br> Web: www.capslock.co.tz
		</td>
		<td align="right" font="color:white"> 
			<a class="btn" href="javascript:print()" title="Print">
				<font class="btn1" color="white">Print</font>
			</a>
			<a class="btn" href="{{url('report/report1')}}" title="Report">
				<font class="btn1" color="white">Back to Report</font>
			</a>
		</td>
	</tr>
</table>

