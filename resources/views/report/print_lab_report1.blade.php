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
		<td align="left"> Report Date : {{date('D, d M Y',strtotime(date($from)))}} -  {{date('D, d M Y',strtotime(date($to)))}} </td>
		<td align="right"> Report Generate at : {{date('H:i, d M Y',strtotime(date('Y-m-d H:i:s')))}} </td>
	</tr>
</table>

@if (isset($summary))
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr><td colspan="6">Laboratory Test Summary</td></tr>
		<tr>
			<td>Lab Test</td>
			<td>Doctor Request</td>
			<td>Times</td>
		</tr>
		@foreach ($summary as $s)
			<tr>
				<td>{{$s->test}}</td>
				<td>{{$s->doctor}}</td>
				<td>{{$s->times}}</td>
			</tr>
		@endforeach
	</table>
@endif

<hr>

@if (isset($changes))
	<table width="100%" cellpadding="5" cellspacing="0" border="1">
		<tr><td colspan="7">Laboratory Test Trends</td></tr>
		<tr>
			<th>Date and Time</th>
			<th>Lab Test</th>
			<th>Patient</th>
			<th>Gender</th>
			<th>Doctor</th>
			<th>Technician</th>
			<th>Results</th>
		</tr>
		@foreach ($changes as $v)
			<tr>
				<td>{{date('H:i d M Y',strtotime($v->created_at))}}</td>
				<td>{{$v->test}}</td>
				<td>{{$v->patient}}</td>
				<td>{{$v->gender}}</td>
				<td>{{$v->doctor}}</td>
				<td>{{$v->technician}}</td>
				<td>{{$v->results_register}}</td>
			</tr>
		@endforeach
			
	</table>
@endif

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
			<a class="btn" href="javascript:history.go(-1)" title="Go Back">
				<font class="btn1" color="white">Go Back</font>
			</a>
		</td>
	</tr>
</table>

