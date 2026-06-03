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
<h3 align="middle">DAVIDSON EMMANUEL SPECIALIZED PAEDIATRIC CENTRE</h3>
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
					<td style="font-size:11px"  colspan="2">P.O Box 7760 Dar-es-Salaam, Sinza Kijiweni</td>
				</tr>
			</table>
		</td>
	</tr>
</table>	


<hr>

<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td align="left"> Ref No : {{$p->id}} </td>
		<td align="right"> Date : {{date('H:i, d M Y',strtotime($p->created_at))}} </td>
	</tr>
	<tr>
		<td align="left">  Patient Name : {{$p->attendance->patient->name}} </td>
		<td align="right"> Cashier : {{$p->creator->name}}  </td>
	</tr>
	<tr>
		<td align="left">  Patient Gender : {{$p->attendance->patient->gender}} </td>
		<td align="right">
			@if ($p->dispensor)
				 Dispensor : {{$p->dispensor->name}} 
			@endif
		</td>
	</tr>
</table>


<table width="100%" cellpadding="5" cellspacing="0" border="1">
	<tr>
		<td align="left">  <b>Bill</b> </td>
		<td align="left"> <b>Description</b> </td>
		<td align="right"> <b>Amount</b> </td>
	</tr>
	@foreach ($p->attendance->bill as $b)
		<tr>
			<td align="left">{{$b->name}}</td>
			<td align="left">{{$b->dosage}}</td>
			<td align="right">{{number_format($b->amount)}} Tshs</td>
		</tr>
	@endforeach
		<tr>
			<td align="left"> Total </td>
			<td align=""></td>
			<td align="right">{{number_format($p->bill)}} Tshs</td>
		</tr>
</table>

@if ($p->insurance_id > 0)
	<hr>

	<table width="100%" cellpadding="2" cellspacing="2" border="0">
		<tr>
			<td> Payment Covered By : {{ucwords(Insurance::find($p->insurance_id)->name)}}</td>
			<td> Patient Membership ID : {{$p->attendance->insurance_number}} </td>
			<td> Sign : _______________________________________ </td>
		</tr>
		
	</table>
@else
	<hr>

	<table width="100%" cellpadding="2" cellspacing="2" border="0">
		<tr>
			<td> Tendered Amount </td>
			<td> {{number_format($p->tendered)}} Tshs </td>
		</tr>
		@if (($p->tendered - $p->bill) >= 0)
			<tr>
				<td> Change </td>
				<td> {{number_format($p->tendered - $p->bill)}} Tshs </td>
			</tr>
		@else 
			<tr>
				<td> Discount Amount </td>
				<td> {{number_format($p->bill - $p->tendered)}} Tshs </td>
			</tr>
		@endif
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
			<a class="btn" href="{{url('dashboard')}}" title="Dashboard">
				<font class="btn1" color="white">Dashboard</font>
			</a>
		</td>
	</tr>
</table>





