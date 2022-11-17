<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Notification::where('user_id', '=', Auth::id())->orderBy('created_at', 'DESC')->get();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function maskAllAsRead()
    {
        $updatedRecords = Notification::where([
            'user_id' => Auth::id(),
            'status' => Notification::STATUS_UNREAD
        ])
            ->update([
                'status' => Notification::STATUS_READ
            ]);

        return ['updated' => $updatedRecords];
    }

    public function maskAsRead($id)
    {
        $updatedRecords = Notification::where([
            'user_id' => Auth::id(),
            'status' => Notification::STATUS_UNREAD,
            'id' => $id
        ])
            ->update([
                'status' => Notification::STATUS_READ
            ]);

        return ['updated' => $updatedRecords];
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $notification = Notification::where([
                'id' => $id,
                'user_id' => Auth::id()
            ])->firstOrFail();

            $notification->delete();

            return 'success';
        } catch (\Throwable $th) {
            return 'failure';
        }
    }

    public function deleteAll()
    {
        try {
            $deletedRecords = Notification::where([
                'user_id' => Auth::id(),
            ])->delete();

            return ['deleted' => $deletedRecords];
        } catch (\Throwable $th) {
            return 'failure';
        }
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
        //
    }
}
