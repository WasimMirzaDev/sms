<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stat;
use App\Models\Student;
use App\Models\EventDetail;
use Illuminate\Support\Facades\DB;
use Response;
use Validator;
class EventController extends Controller
{

public function add(Request $request)
{

  $status = Stat::all();
  $list = Student::all();
  if(empty($request->id))
  {
    $stat_id = 0;
    $title = $note = "";
    $event_students = [];
    extract($request->all());
    // dd(date('Y-m-d', strtotime($start)));
    return response()->json([
      'statusCode' => 200,
      'html' => view('event-detail', get_defined_vars())->render(),
    ]);
  }
  else
  {
    $ed = Event::whereId($request->id)->first();
    $id = $ed->id;
    $title = $ed->title;
    $note = $ed->note;
    $start = $ed->start;
    $end = $ed->end;
    $stat_id = $ed->stat_id;
    $event_students = EventDetail::where('event_id', $request->id)->pluck('student_id')->toArray();
    return response()->json([
      'statusCode' => 200,
      'html' => view('event-detail', get_defined_vars())->render(),
    ]);
  }
}

  public function index()
  {
      if(request()->ajax())
      {
       $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
       $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
       $data = DB::SELECT("SELECT e.id, e.title, e.start, date_add(e.end, interval 1 day) as end, s.color
       FROM events
       as e
       inner join stats as s on s.id = e.stat_id
--       WHERE start >= $start AND end <= $end
       ");
       // $data = Event::with('stat')->whereDate('start', '>=', $start)->whereDate('end',   '<=', $end)->get();
       return json_encode($data);
      }
      return view('events', get_defined_vars());
  }


  public function create(Request $request)
  {
    $event_id   = $request->id;
    $student_id = $request->student_id;

    $validator = Validator::make($request->all(), [
      'title' => 'required',
    //   'number' => 'required|unique:units,number,' . $request->id,
      'start' => 'required',
      'end' => 'required',
      'stat_id' => 'required',
      'student_id' => 'required',
    ]);



if ($validator->passes()) {
  $insertArr = [
    'title' => $request->title,
    'stat_id' => $request->stat_id,
    'note' => $request->note,
    'start' => date('Y-m-d, H:i:s', strtotime($request->start)),
    'end' => date('Y-m-d, H:i:s', strtotime($request->end)),
  ];


  $event =  Event::updateOrCreate(['id' => $event_id],$insertArr);

  if($event)
  {
    $event_id = $event->id;
    EventDetail::where('event_id', $event_id)->delete();
    foreach($student_id as $st)
    {
      $ed = new EventDetail;
      $ed->student_id = $st;
      $ed->event_id   = $event_id;
      $ed->save();
    }
    $event->color = Stat::whereId($event->stat_id)->pluck('color');
    $event->start = date('Y-m-d H:i:s',strtotime($event->start));
    $event->end   = date('Y-m-d H:i:s',strtotime($event->end.'+1day'));

    return response()->json(['success'=>1, 'msg'=>'Saved Successfully!', 'data' => $event]);
  }
}
      return response()->json(['success' => 0, 'msg'=>$validator->errors()->all()]);
  }


  public function update(Request $request)
  {
      $where = array('id' => $request->id);
      $updateArr = ['title' => $request->title,'start' => $request->start, 'end' => $request->end];
      $event  = Event::where($where)->update($updateArr);

      return Response::json($event);
  }


  public function destroy(Request $request)
  {
      $event = Event::where('id',$request->id)->delete();
      if($event)
      {
          EventDetail::where('event_id', $request->id)->delete();
          return response()->json(['success'=>1, 'msg'=>'Deleted Successfully!', 'id'=>$request->id]);
      }
          return response()->json(['success'=>0, 'msg'=>'Error in deleting record!']);
  }

}