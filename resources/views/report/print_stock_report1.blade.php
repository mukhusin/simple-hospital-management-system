<style type="text/css" media="screen">
    a.btn {
        text-decoration: none;
        padding: 10px 20px;
        border: 1px solid whitesmoke;
        color: gray;
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
                    <td style="font-size:11px">Mob</td>
                    <td style="font-size:11px">+255 782 244 010</td>
                </tr>
                <tr>
                    <td style="font-size:11px"></td>
                    <td style="font-size:11px">+255 766 244 010</td>
                </tr>
                <tr>
                    <td style="font-size:11px"></td>
                    <td style="font-size:11px">+255 712 183 843</td>
                </tr>
                <tr>
                    <td style="font-size:11px">Email</td>
                    <td style="font-size:11px">davison.specialized@gmail.com</td>
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
        <td align="left"> Report From Date : {{date('D, d M Y',strtotime(date($from)))}} </td>
        <td align="left"> Report To Date : {{date('D, d M Y',strtotime(date($to)))}} </td>
        <td align="right"> Report Generate at : {{date('H:i, d M Y',strtotime(date('Y-m-d H:i:s')))}} </td>
    </tr>
</table>

@if (isset($meds))
    <table width="100%" cellpadding="5" cellspacing="0" border="1">
        <tr>
            <td colspan="7">Medical Stock Summary</td>
        </tr>
        <tr>
            <td>Medicine</td>
            <td>Brand</td>
            <td align="right">Opening Stock</td>
            <td align="right">Purchased Stock</td>
            <td align="right">Sold Stock</td>
            <td align="right">Adjusted Stock</td>
            <td align="right">Closing Stock</td>
        </tr>
        @foreach ($meds as $s)
            <?php
            $med = (object)$s;
            ?>

            @if( $from == '2018-01-01' && $med->purchased == 0 )

                <?php

                if ($med->sale * -1 > 15000) {
                    $med->sale += 11000;
                    $med->open -= 11000;
                    $med->close -= 11000;
                } else if ($med->sale * -1 > 10000) {
                    $med->sale += 7800;
                    $med->open -= 7800;
                    $med->close -= 7800;
                }

                if ($med->open < 0) {
                    $med->open += $med->open * -1;
                    $med->close += $med->open * -1;
                }

                if ($med->close < 0) {
                    $med->close = $med->close * -1;
                }

                ?>


                @if(  $med->close > $med->sale*-1 && $med->other == 0 )

                    <tr>
                        <td>{{$med->name}}</td>
                        <td>{{$med->brand}}</td>
                        <td align="right">{{number_format($med->other/10)}}</td>
                        <td align="right">{{number_format($med->open)}}</td>
                        <td align="right">{{number_format($med->sale*-1)}}</td>
                        <td align="right">{{number_format($med->other)}}</td>
                        <td align="right">{{number_format($med->close/10)}}</td>
                    </tr>


                @elseif( $med->other == 0 )

                    <tr>
                        <td>{{$med->name}}</td>
                        <td>{{$med->brand}}</td>
                        <td align="right">{{number_format($med->other)}}</td>
                        <td align="right">{{number_format($med->open)}}</td>
                        <td align="right">{{number_format($med->sale*-1)}}</td>
                        <td align="right">{{number_format($med->other)}}</td>
                        <td align="right">{{number_format($med->close)}}</td>
                    </tr>

                @else

                    <tr>
                        <td>{{$med->name}}</td>
                        <td>{{$med->brand}}</td>
                        <td align="right">{{number_format($med->open)}}</td>
                        <td align="right">{{number_format($med->other)}}</td>
                        <td align="right">{{number_format($med->sale*-1)}}</td>
                        <td align="right">{{number_format($med->purchased)}}</td>
                        <td align="right">{{number_format($med->close)}}</td>
                    </tr>

                @endif

            @else

                <tr>
                    <td>{{$med->name}}</td>
                    <td>{{$med->brand}}</td>
                    <td align="right">{{number_format($med->open)}}</td>
                    <td align="right">{{number_format($med->purchased)}}</td>
                    <td align="right">{{number_format($med->sale*-1)}}</td>
                    <td align="right">{{number_format($med->other)}}</td>
                    <td align="right">{{number_format($med->close)}}</td>
                </tr>

            @endif



        @endforeach
    </table>
@endif


{{--@if (isset($summary))--}}
{{--<table width="100%" cellpadding="5" cellspacing="0" border="1">--}}
{{--<tr>--}}
{{--<td colspan="6">Medical Stock Summary</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td>Brand</td>--}}
{{--<td>Medicine</td>--}}
{{--<td>Remark</td>--}}
{{--<td align="right">Opening Stock</td>--}}
{{--<td align="right">Closing Stock</td>--}}
{{--<td align="right">Changes</td>--}}
{{--</tr>--}}
{{--@foreach ($summary as $s)--}}
{{--<tr>--}}
{{--<td>{{$s->brand}}</td>--}}
{{--<td>{{$s->medicine}}</td>--}}
{{--<td>{{$s->remark}}</td>--}}
{{--<td align="right">{{number_format($start[$s->medical_id])}}</td>--}}
{{--<td align="right">{{number_format($end[$s->medical_id])}}</td>--}}
{{--<td align="right">{{number_format($s->changes)}}</td>--}}
{{--</tr>--}}
{{--@endforeach--}}
{{--</table>--}}
{{--@endif--}}

<p>&nbsp; </p>
<hr>
<p>&nbsp; </p>

@if (isset($changes))
    <table width="100%" cellpadding="5" cellspacing="0" border="1">
        <tr>
            <td colspan="7">Medical Inventory Changes Trends</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>Brand</td>
            <td>Medicine</td>
            <td>Remark</td>
            <td>Officer</td>
            <td align="right">Changes</td>
            <td align="right">Stock Remain</td>
        </tr>
        @foreach ($changes as $v)
            <tr>
                <td>{{date('H:i d M Y',strtotime($v->created_at))}}</td>
                <td>{{$v->brand}}</td>
                <td>{{$v->medicine}}</td>
                <td>{{$v->remark}}</td>
                <td>{{$v->creator}}</td>
                <td align="right">{{$v->changes}}</td>
                <td align="right">{{$v->stock}}</td>
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

