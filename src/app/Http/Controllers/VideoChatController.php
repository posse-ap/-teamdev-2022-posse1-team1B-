<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class VideoChatController extends Controller
{
    public function index(Request $request)
    { // ビデオチャットページ

        $user = $request->user();
        $others = \App\Models\User::where('id', '!=', $user->id)->pluck('first_name', 'id');
        return view('video_chat.index')->with([
            'user' => collect($request->user()->only(['id', 'first_name'])),
            'others' => $others,
        ]);

    }

    public function auth(Request $request)
    { // Pusherの認証

        $user = $request->user();
        $socket_id = $request->socket_id;
        $channel_name = $request->channel_name;
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'encrypted' => true,
            ]
        );
        return response(
            $pusher->presence_auth($channel_name, $socket_id, $user->id)
        );

    }
}