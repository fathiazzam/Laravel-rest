<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use App\User;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $meetings = Meeting::all();
       foreach ($meetings as $meeting ) {
          $meeting->view_meeting = [
              'href' => 'api/v1/meeting' .$meeting->id,
              'method' => 'GET',
          ];
       }
       $response = [
           'msg' => 'List semua Meeting',
           'meetings' => $meetings
       ];
       return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);
        $title = $request->input('title');
        $description = $request->input('description'); 
        $time = $request->input('time'); 
        $user_id = $request->input('user_id'); 

        $meeting = new Meeting([
            'time' => $time,
            'title' => $title,
            'description' => $description
        ]);

        if ($meeting->save()) {
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting' .$meeting->id,
                'method' => 'GET',
            ];
            $message = [
                'msg' => 'Meeting Created',
                'meeting' => $meeting
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg' => 'Telah terjadi error'
        ];
        return response()->json($respons, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')
                    ->where('id', $id)
                    ->firstOrFail();
        $meeting->view_meeting= [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];
        $response = [
            'msg' => 'Informasi Meeting',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);
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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);
        $title = $request->input('title');
        $description = $request->input('description'); 
        $time = $request->input('time'); 
        $user_id = $request->input('user_id');  

        $meeting = Meeting::with('users')
                    ->findOrfail($id);
        if (!$meeting->users()
             ->where('users.id', $user_id)->first()) {
            return response()->json(['msg' => 'anda tidak punya hak'], 401);
        };

        $meeting->time = $time;
        $meeting->title = $title;
        $meeting->description = $description;

        if (!$meeting->update()) {
            return response()->json([
                'msg' => 'eror waktu update'
            ], 404);
        }
        $meeting->view_meeting = [
            'href' => 'api/v1/meeting/' . $meeting->id,
            'method' => 'GET',
        ];
        $response = [
            'msg' => 'meeting telah di update',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users();
        $meeting->users()->detach();

        if (!$meeting->delete()) {
            foreach ($users as $user) {
                $meeting->user()->attach($user);
            }
            return response()->json(
                ['msg' => 'Delete gagal'], 404);
        }
        $response = [
            'msg' => 'Meeting telah dihapus',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
        ];
        return response()->json($response, 200);
    }
}
