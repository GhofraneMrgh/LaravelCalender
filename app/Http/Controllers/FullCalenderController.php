<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
//use App\Event;
use App\Models\Event;
use Calendar;
use DB; 
  
class FullCalenderController extends Controller
{

    public function home(){
        return redirect('fullcalender');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(Request $request)
    {
  
        if($request->ajax()) {
       
             $data = Event::whereDate('start', '>=', $request->start)
                       ->whereDate('end',   '<=', $request->end)
                       ->get(['id','start_time','end_time','title', 'start', 'end']);
  
             return response()->json($data);
        }
  
        return view('fullcalender');
    }
 
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function ajax(Request $request)
    {
        if ($request->type=='add') {
           $event = Event::create([
                  'title' => $request->title,
                  'start_time' => $request->start_time,
                  'end_time' => $request->end_time,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
 
        }elseif ($request->type=='update') {
            $event = Event::find($request->id)->update([
                  'title' => $request->title,
                  'start' => $request->start,
                  'end' => $request->end,
              ]);
        }elseif ($request->type=='delete') {
            $event = Event::find($request->id)->delete();
        }

        return response()->json($event);

        
    }
     public function addEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'start' => 'required'
        ]);
 
        if ($validator->fails()) {
            \Session::flash('warnning','Please enter the valid details');
            return Redirect::to('/fullcalender')->withInput()->withErrors($validator);
        }

        $exist= DB::table('events')
        ->where('start',$request['start'])
        ->where('start_time',$request['start_time'])
        ->first();


        if ($exist) {

        \Session::flash('warnning','Event already existed.');
        return Redirect::to('/fullcalender');

        }else{

        $event = Event::create([
                  'title' => $request->title,
                  'start_time' => $request->start_time,
                  'end_time' => $request->end_time,
                  'start' => $request->start,
                  'end' => $request->start,
              ]);
 
        \Session::flash('success','Event added successfully.');
        return Redirect::to('/fullcalender');
        }
        
    }
}