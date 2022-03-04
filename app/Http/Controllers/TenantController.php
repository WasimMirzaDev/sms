<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\FeeHead;
use Illuminate\Support\Facades\DB;
use App\Models\Enrolment;
use App\Models\EnrolmentD;
use App\Models\PaymentSchedule;
use App\Models\Daysofweek;
use App\Models\TenantUnit;
use Validator;
class TenantController extends Controller
{
  /**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{

  $list = Tenant::all();
  $tu = TenantUnit::pluck('id');
  // dd($tu);
  $units = Unit::with('building')->whereNotIn('units.id', $tu)->orderBy('units.building_id', 'asc')->orderBy('units.name', 'asc')->get();
  
  $fee_head = FeeHead::where('fh_active', 1)->get();
  // dd($fee_head);
  $next_number = Tenant::max('number')+1;
  $dow = Daysofweek::all();
  $ps = PaymentSchedule::all();
    return view('tenants', get_defined_vars());
}

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public function create()
{
    //
}

/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(Request $request)
{

  // dd($request);
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'number' => 'required|unique:tenants,number,' . $request->id,
      'cell' => 'required',
      'file' => 'mimes:jpg,jpeg,png, svg, JPG, JPEG, PNG, SVG|max:8000',
    ]);
    // dd($request->all());

    $building_id = Unit::where('id', $request->unit_id)->pluck('building_id')->first();

    // dd($request->all());
    $x = $request->all();
    $x = (object)$x;
    $x->number = empty($x->number) ? 0 : $x->number;
    $x->joining_date = date('Y-m-d', strtotime($x->joining_date));
    $x->monthly_datenum = empty($x->monthly_datenum) ? 0 : $x->monthly_datenum;
    $x->advance = empty($x->advance) ? 0 : $x->advance;
    $x->opening = empty($x->opening) ? 0 : $x->opening;
    $x->yearly_month_date = date('m-d', strtotime($x->yearly_month_date));

    if ($validator->passes()) {
      $tenant   =   tenant::updateOrCreate(
                      [
                          'id' => $request->id
                      ],
                      (array)$x);
      if($tenant)
      {
        $tenant_id = $tenant->id;

        // upload file
        $licence = $misc = $family = $t_image = $lease = "";

        if($request->hasFile("file"))
        {
          $fileName = $tenant_id.".".$request->file->extension();
          $t_image = $request->file->extension();
          $request->file->move(public_path('uploads/tenants/'), $fileName);
           $updateable = Tenant::find($tenant_id);
           $updateable->t_image = $t_image;
           $updateable->update();
        }
        if($request->hasFile("licence"))
        {
          $fileName = $tenant_id.".".$request->licence->extension();
          $licence = $request->licence->extension();
          $request->licence->move(public_path('uploads/tenants/licence/'), $fileName);
          $updateable = Tenant::find($tenant_id);
          $updateable->licence = $licence;
          $updateable->update();
        }

        if($request->hasFile("family"))
        {
          $fileName = $tenant_id.".".$request->family->extension();
          $family = $request->family->extension();
          $request->family->move(public_path('uploads/tenants/family/'), $fileName);
          $updateable = Tenant::find($tenant_id);
          $updateable->family = $family;
          $updateable->update();
        }

        if($request->hasFile("misc"))
        {
          $fileName = $tenant_id.".".$request->misc->extension();
          $misc = $request->misc->extension();
          $request->misc->move(public_path('uploads/tenants/misc/'), $fileName);
          $updateable = Tenant::find($tenant_id);
          $updateable->misc = $misc;
          $updateable->update();
        }

        if($request->hasFile("lease"))
        {
          $fileName = $tenant_id.".".$request->lease->extension();
          $lease = $request->lease->extension();
          $request->lease->move(public_path('uploads/tenants/lease/'), $fileName);
          $updateable = Tenant::find($tenant_id);
          $updateable->lease = $lease;
          $updateable->update();
        }

        $en = Enrolment::where([['unit_id', '=', $request->unit_id],
        ['tenant_id', '=', $request->id]
        ])->first();

        if(!empty($request->id) && !empty($en->id))
        {
          $en_id = $en->id;
          $en->building_id = $building_id;
          $en->date = $x->joining_date;
          $en->unit_id = $x->unit_id;
          $en->tenant_id = $tenant_id;
          $en->update();
        }
        else
        {
          $en = new Enrolment;
          $en->building_id = $building_id;
          $en->date = $x->joining_date;
          $en->unit_id = $x->unit_id;
          $en->tenant_id = $tenant_id;
          $en->active = 1;
          $en->save();
          $en_id = $en->id;
        }
        if($en)
        {
          if(!empty($request->fh_amount))
          {
            EnrolmentD::where('enrolment_id', $en_id)->delete();
            for($a=0; $a<count($request->fh_id); $a++)
            {
              if(!empty($request->fh_amount[$a]))
              {
                $end = new EnrolmentD;
                $end->enrolment_id = $en_id;
                $end->fh_id = $request->fh_id[$a];
                $end->amount = $request->fh_amount[$a];
                $end->save();
              }
            }
          }
        }

        if($request->active == 1)
        {
          $tu = TenantUnit::where([['tenant_id', '=', $tenant_id],['unit_id', '=', $request->unit_id]])->delete();
          $check = TenantUnit::where('unit_id', '=', $request->unit_id)->first();
          if(empty($check))
          {
            $tu = new TenantUnit;
            $tu->unit_id = $request->unit_id;
            $tu->tenant_id = $tenant_id;
            $tu->save();
          }
          else
          {
            return response()->json(['success' => 0, 'msg'=>['Sorry, This unit is already assigned to someone...']]);
          }
        }
        else
        {
          $tu = TenantUnit::where([['tenant_id', '=', $tenant_id],['unit_id', '=', $request->unit_id]])->delete();
        }



        // $update_images = [
        //   'licence'  => $licence,
        //   'misc'  => $misc,
        //   'family'  => $family,
        //   't_image'  => $t_image,
        //   'lease'     => $lease
        // ];

        //

        $r = $request->all();
        $r['id'] = $tenant_id;
        return response()->json(['success'=>1, 'msg'=>'Saved Successfully!', 'data' => $r]);
      }
    }
    return response()->json(['success' => 0, 'msg'=>$validator->errors()->all()]);
}

/**
 * Display the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function show($id)
{
    //
}

/**
 * Show the form for editing the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function edit($id)
{
  $t = Tenant::find($id);
  $file = asset("app_images/default.jpg");
  if(is_file(public_path()."/uploads/tenants/".$t->id.".jpg"))
    {
      $file = asset("/uploads/tenants/".$t->id.".jpg");
    }
  if(is_file(public_path()."/uploads/tenants/".$t->id.".png"))
    {
      $file = asset("/uploads/tenants/".$t->id.".png");
    }
  if(is_file(public_path()."/uploads/tenants/".$t->id.".svg"))
    {
      $file = asset("/uploads/tenants/".$t->id.".svg");
    }
    $t['file'] = $file;


    $list = Tenant::all();
    // $units = Unit::with('building')->where('units.booked', '=', '0')->orderBy('units.building_id', 'asc')->orderBy('units.name', 'asc')->get();
    $units = DB::SELECT("
    SELECT u.id, b.name as building_name, u.name as unit_name FROM units as u INNER JOIN buildings AS b ON b.id = u.building_id WHERE u.id not in (select unit_id from tenant_units)
    UNION ALL
     SELECT u.id, b.name as building_name, u.name as unit_name FROM units as u INNER JOIN buildings AS b ON b.id = u.building_id inner join tenant_units as t ON t.unit_id = u.id WHERE t.tenant_id = $t->id
    ORDER BY 2,3
    ");
    // dd($units);

    $fee_head = FeeHead::where('fh_active', 1)->get();
    // dd($fee_head);
    $next_number = $t->number;

    $dow = Daysofweek::all();
    $ps = PaymentSchedule::all();
    $enrolment_id = Enrolment::where([['tenant_id', '=', $id], ['unit_id', '=', $t->unit_id]])->pluck('id')->first();
    $end = EnrolmentD::where('enrolment_id', $enrolment_id)->get();
    return view('tenants', get_defined_vars());
  // return response()->json($Tenant);
}

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function update(Request $request, $id)
{
    //
}

/**
 * Remove the specified resource from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function destroy($id)
{
    $deleted = Tenant::find($id)->delete();
    if($deleted)
    {
      $file_pattern = public_path()."/uploads/tenants/".$id.".*";  // Assuming your files are named like profiles/bb-x62.foo, profiles/bb-x62.bar, etc.
        array_map( "unlink", glob( $file_pattern ) );
      return response()->json(['success'=>1, 'msg'=>'Deleted Successfully!']);
    }
    return response()->json(['success'=>0, 'msg'=>'Error in deleting record!']);
}
}
