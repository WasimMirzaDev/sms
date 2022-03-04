<!DOCTYPE html>
<html>
<head>
    <title>Rent Receipt</title>
    <style media="screen">
    @page { margin: 20px; }
body { margin: 0px; }
    </style>
</head>
<body style="font-size:16px;">
    <center>
    <b style="font-size:20px;">
        {{$cmp_name}}
    </b>
    </center>
    <center>
        <span style="font-size:12px;">
            {{$cmp_address}}
        </span>
    </center>
    <br>
    <center>
        <b style="font-size:18px;">Payment Receipt</b>
    </center>
    Rct #
<b>{{$rct_no}}</b>
<br>
<span style="font-size:12px;">{{$address}}, Unit: {{$unit}}</span>
<fieldset>
  <table width="100%" border="0">
    <tr>
      <td width="80%">
        <span style="font-size:25px;">
          <b>${{$rct_amt}}</b>
        </span>

        <br>
            Received From: <b>{{$customer}}</b>
            <br>
            Payment Method: <b>{{$pm_name}}</b>
            <br>
            @if ($pending_amt > 0)
                Balance Due: <b>(${{$pending_amt}})</b>
            @else
                Balance Due: <b>${{$pending_amt}}</b>
            @endif
      </td>

      <td width="50%" style="font-size:12px;">
        <b style="width:20px;">
          Voucher Detail
        </b>
        <br>
        Voucher # <b>{{$vch_id}}</b>
        <br>
        @foreach($vch_detail as $d)
          {{$d->fh_name}}: <b>${{$d->fh_amount}}</b>
          <br>
        @endforeach
        <!-- Pet Due: $60 -->
      </td>
    </tr>
  </table>
</fieldset>
<center>
    Thank You
    <br>
<b>{{$date}}</b>
</center>
</body>
</html>
