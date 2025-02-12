<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use App\User;

class RegistrationController extends Controller
{
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required',
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');

        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);
        
        $message = [
            'msg' => 'user sudah terdaftar di meeting',
            'user' => $user,
            'meeting' => $meeting,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];

        if ($meeting->users()
                    ->where('users.id', $user->id)
                    ->first()) {
            return response()->json($message, 404);
        }

        $user->meetings()->attach($meeting);
        $response = [
            'msg' => 'user terdaftar dalam meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
            ];
        return response()->json($response, 201);
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
        $meeting->users()->detach();

        $response = [
            'msg' => 'user tidak terdaftar dalam meeting',
            'meeting' => $meeting,
            'user' => 'tbd',
            'unregister' => [
                'href' => 'api/v1/meeting/registration/',
                'method' => 'POST',
                'params' => 'user_id', 'meeting_id'
            ]
            ];
        return response()->json($response, 200);

    }
}
