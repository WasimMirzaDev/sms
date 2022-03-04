<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\FeeHead;
use App\Models\Tenant;
use App\Models\TenantUnit;
use App\Models\Enrolment;
use App\Models\EnrolmentD;
use App\Models\Daysofweek;
use App\Models\Unit;
use App\Models\Challan;
use App\Models\ChallanDetail;
use App\Models\PaymentMethod;
use App\Models\Receiving;
use App\Models\Adjustment;
use Validator;
use Illuminate\Support\Facades\DB;

use PDF;
class ReceivingController extends Controller
{
  public function index()
  {
     $tenant = DB::SELECT("
     SELECT u.name as unit_name, b.name as address_name, tu.unit_id, t.id as tenant_id, t.name as tenant_name FROM tenants AS t
     LEFT OUTER JOIN tenant_units as tu ON tu.tenant_id = t.id
     LEFT OUTER JOIN units AS u ON u.id = tu.unit_id
     LEFT OUTER JOIN buildings AS b ON b.id = u.building_id
     ");
     $receipts = Receiving::all();
     return view('receivings', get_defined_vars());
  }

  public function get_receivables(Request $request)
  {
    $ra = DB::SELECT("
    SELECT c.tenant_id, c.date, c.challan_id as id, c.remarks, b.name as building_name, u.name as unit_name, (CASE WHEN (r_amt > 0 AND r_amt IS NOT NULL) AND c_amt > r_amt THEN 'Partial Paid' WHEN (r_amt = 0) OR r_amt IS NULL THEN 'Unpaid' ELSE 'Paid' END) AS status, c_amt AS vch_total, ifnull(c_amt, 0) - ifnull(r_amt, 0) AS receiveable_amt FROM
    (
     select v.tenant_id, v.date, v.remarks, v.unit_id, challan_id, sum(fh_amount) as c_amt
     FROM challan_details
     JOIN challans AS v ON v.id = challan_details.challan_id
     GROUP BY v.tenant_id, v.date, v.id, v.remarks, v.unit_id, challan_id) AS c

    LEFT OUTER JOIN (select challan_id, sum(amount) as r_amt FROM receivings GROUP BY challan_id)
    AS r ON r.challan_id = c.challan_id

    LEFT OUTER JOIN units AS u ON u.id = c.unit_id
    LEFT OUTER JOIN buildings AS b ON b.id = u.building_id

    WHERE (c_amt <> r_amt || r_amt IS NULL) AND c.tenant_id = $request->tenant_id
    ");
    return response()->json([
        'statusCode' => 200,
        'html' => view('receivables', get_defined_vars())->render(),
    ]);
  }

  public function Getsuffix($number)
  {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if (($number %100) >= 11 && ($number%100) <= 13)
       $abbreviation = $number. 'th';
    else
       $abbreviation = $number. $ends[$number % 10];

       return $abbreviation;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */

   public function vch_rem($voucher_id)
   {
     $rem = DB::SELECT("
     SELECT SUM(amt) AS rem_amt FROM (
     SELECT SUM(fh_amount) AS amt FROM challan_details WHERE challan_id = $voucher_id
     UNION ALL
     SELECT SUM(amount)*-1 AS amt FROM receivings WHERE challan_id = $voucher_id
         ) AS trn
     ");
     return $rem[0]->rem_amt;
   }

  public function create(Request $request)
  {
    $rem_amt = $this->vch_rem($request->vch_id);
    $request = Challan::find($request->vch_id);
    $pm = PaymentMethod::all();
    $tenant = Tenant::find($request->tenant_id);
    $ps = $tenant->pm_type;
    $pays_on = "";

    if($ps == 'd')
    {
      $pays_on = "Daily";
    }
    if($ps == 'w')
    {

       $day = Daysofweek::where('number', $tenant->weekly_daynum)->first();

       $pays_on = !empty($day->name) ? "on every ".$day->name : '';
    }

    if($ps == 'm')
    {
       $day = $tenant->monthly_datenum;
       $pays_on = !empty($day->name) ? "on every ".$day->name : '';
    }
    if($ps == 'y')
    {
      $pays_on = !empty($day->name) ? "on every ".date('d F', strtotime($tenant->yearly_month_date)) : '';
    }


     return response()->json([
         'statusCode' => 200,
         'html' => view('receiving-content', get_defined_vars())->render(),
     ]);
  }


  public function adjust_fee_head($challan_id, $rec_id)
  {
    $total_receiving = Receiving::where('id', $rec_id)->sum('amount');

    Adjustment::where('receiving_id', $rec_id)->delete();

    // $challan_detail = ChallanDetail::where('challan_id', $challan_id)->orderBy('id')->get();

    $challan_detail = DB::select("
    select fh_id, sum(fh_amount) as fh_amount from (
    select fh_id, sum(fh_amount) as fh_amount from challan_details WHERE challan_id = $challan_id group by fh_id
    union all
    select fh_id, sum(0-adj_amount) as fh_amount from adjustments WHERE challan_id = $challan_id group by fh_id
        ) as x GROUP BY fh_id
    ");

    for($i = 0; $i<count($challan_detail); $i++)
    {
      $fh_id = $challan_detail[$i]->fh_id;
      $fh_amount = $challan_detail[$i]->fh_amount;

        if($total_receiving <= 0)
        {
          break;
        }

        if($total_receiving > $fh_amount)
        {
          $adj_amount = $fh_amount;
          $total_receiving = $total_receiving - $adj_amount;
        }
        else
        {
          $adj_amount = $total_receiving;
          $total_receiving = 0;
        }

        $adj = new Adjustment;
        $adj->fh_id = $fh_id;
        $adj->adj_amount = $adj_amount;
        $adj->challan_id = $challan_id;
        $adj->receiving_id = $rec_id;
        $adj->save();
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $vch_pending = $this->vch_rem($request->vch_id);
    if($request->amount == 0)
    {
      return response()->json(['success' => 0, 'msg'=>['Error..! Cannot receive 0 amount.']]);
    }
    if($request->id > 0)
    {
      $rcvd_amt = Receiving::whereId($request->id)->pluck('amount')->first();
      $vch_pending = $vch_pending + $rcvd_amt;
    }

    if($vch_pending < $request->amount)
    {
      return response()->json(['success' => 0, 'msg'=>['Error..! Receiving Cannot be greater then receivables.']]);
    }

    $validator = Validator::make($request->all(), [
      'date' => 'required',
      'tenant_id' => 'required',
      'unit_id' => 'required',
      'vch_id' => 'required',
      'amount' => 'required',
      'date'     => 'required'
    ]);
    $fh_id = $request->fh_id;
    $fh_amount = $request->fh_amount;

    $challan = Challan::whereId($request->vch_id)->first();



    if ($validator->passes()) {
      $receiving   =   Receiving::updateOrCreate(
                      [
                          'id' => $request->id
                      ],
                      [
                        'date'    => date('Y-m-d', strtotime($request->date)),
                        'challan_id'    => $challan->id,
                        'tenant_id' => $challan->tenant_id,
                        'unit_id'   => $challan->unit_id,
                        'amount'   =>  $request->amount,
                        'pm_id'   =>  $request->pm_id,
                        'cheque_no'   =>  $request->cheque_no,
                        'remarks'   =>  $request->remarks,
                      ]);
      if($receiving)
      {
        $b = array_merge($request->all());
        $b['id'] = $receiving->id;
        $b['tenant_name'] = $challan->tenant->name;
        $b['unit_name'] = $challan->unit->name;
        $b['address_name']  = $challan->unit->building->name;
        $b['pm_name']  = $receiving->pm->name;
        $msg = "Saved successfully..!";
        if($request->id > 0)
        {
          $msg = "Updated successfully..!";
        }
        $this->adjust_fee_head($challan->id, $receiving->id);
        return response()->json(['success'=>1, 'msg'=>$msg, 'data' => $b]);
      }
    }
    return response()->json(['success' => 0, 'msg'=>$validator->errors()->all()]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Product  $extra
   * @return \Illuminate\Http\Response
   */
  public function show(Product $extra)
  {
      return view('extras.show',compact('extra'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Product  $extra
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {

    $r = Receiving::find($id);
    // dd($r);
    $request = Challan::find($r->challan_id);
    $rem_amt = $this->vch_rem($request->id);
    $rem_amt = $rem_amt + $r->amount;
    $pm = PaymentMethod::all();
    $tenant = Tenant::find($request->tenant_id);
    $ps = $tenant->pm_type;
    $pays_on = "";
    if($ps == 'd')
    {
      $pays_on = "Daily";
    }
    if($ps == 'w')
    {

       $day = Daysofweek::where('number', $tenant->weekly_daynum)->first();

       $pays_on = !empty($day->name) ? "on every ".$day->name : '';
    }

    if($ps == 'm')
    {
       $day = $tenant->monthly_datenum;
       $pays_on = !empty($day->name) ? "on every ".$day->name : '';
    }
    if($ps == 'y')
    {
      $pays_on = !empty($day->name) ? "on every ".date('d F', strtotime($tenant->yearly_month_date)) : '';
    }


     return response()->json([
         'statusCode' => 200,
         'html' => view('receiving-content', get_defined_vars())->render(),
     ]);
  }

  public function thermal_print($id)
  {
    $data = [
        'cmp_name' => 'Fifth Avenue Apartment',
        'cmp_address'  => '932 Lake Street Dalton, Georgia 30721',
        'address'  => '914 Redwine street',
        'unit'     => '1',
        'date'     => date('d-F-Y', strtotime('01-01-2022')),
        'customer' => 'Wasim Akram',
        'rct_amt'  => '160',
        'pending_amt' => '106',
        'rct_no'    => '1'
    ];

    $pdf = PDF::loadView('rec-thermal-print', $data);
    $pdf->setPaper(array(0,0,150,200));

    // return $pdf->download('itsolutionstuff.pdf');
    return $pdf->stream();
  }

  public function print($id)
  {

    $rct = Receiving::whereId($id)->first();
    $vch_id = $rct->challan_id;

    $vch_detail = DB::select(
                                  "SELECT fh_name, cd.fh_amount FROM challan_details as cd
                                  LEFT OUTER JOIN fee_heads as fh ON fh.fh_id = cd.fh_id
                                  WHERE challan_id = $vch_id"
                                );
     $rd = DB::select("
        SELECT pm.name as pm_name, a.name as address_name, u.name as unit_name, t.name as tenant_name, r.date as rct_date, r.amount as rct_amt
            FROM receivings AS r
            LEFT OUTER JOIN tenants AS t ON t.id = r.tenant_id
            LEFT OUTER JOIN units AS u ON u.id = r.unit_id
            LEFT OUTER JOIN buildings AS a ON a.id = u.building_id
            LEFT OUTER JOIN payment_methods as pm ON pm.id = r.pm_id
        WHERE r.id = $id
     ");
     $rd = $rd[0];
     $pending_amount = DB::select(
       "
       select sum(amt) as pending_amount FROM (
       SELECT sum(fh_amount) as amt FROM challan_details as cd
       INNER JOIN challans as c ON c.id = cd.challan_id WHERE c.tenant_id = $rct->tenant_id
       union all
       select sum(0-amount) as amt FROM receivings WHERE tenant_id = $rct->tenant_id
            ) as x
       "
     );
     $pending_amount = $pending_amount[0]->pending_amount;

    $data = [
        'cmp_name' => 'Fifth Avenue Apartment',
        'cmp_address'  => '932 Lake Street Dalton, Georgia 30721',
        'address'  => '914 Redwine street',
        'unit'     => $rd->unit_name,
        'date'     => date('F-d-Y', strtotime($rd->rct_date)),
        'customer' => $rd->tenant_name,
        'rct_amt'  => $rd->rct_amt,
        'pending_amt' => $pending_amount,
        'rct_no'    => $id,
        'vch_detail' => $vch_detail,
        'vch_id'    => $vch_id,
        'pm_name'   => $rd->pm_name
        ];

    $pdf = PDF::loadView('rec-print', $data);
    return $pdf->stream(date('m_d_Y', strtotime($rd->rct_date)).'_'.$id.'.pdf');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Product $product)
  {
      $request->validate([
          'name' => 'required',
          'detail' => 'required',
      ]);

      $product->update($request->all());

      return redirect()->route('products.index')
                      ->with('success','Product updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
   public function destroy($id)
   {
        $voucher_id = Receiving::whereId($id)->pluck('challan_id')[0];
       $deleted = Receiving::find($id)->delete();
       if($deleted)
       {
         $this->adjust_fee_head($voucher_id, $id);
         return response()->json(['success'=>1, 'msg'=>'Deleted Successfully!']);
       }
       return response()->json(['success'=>0, 'msg'=>'Error in deleting record!']);
   }

}
