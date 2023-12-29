<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function store()
    {
        $user = JWTAuth::user();

        if (!$user) {
            return response(['errors' => 'User not found'], 404);
        }

        Order::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $order = Order::all()->sortByDesc('id')->first();

        return response(['order' => $order, 'message' => 'Order Successfully Created!'], 201);
    }

    public function storeDetail(Request $request) {
        $rules = [
            'order_id' => 'required|integer|min:0',
            'movie_schedule_id' => 'required|integer|min:0',
            'seat_id' => 'required|integer|min:0',
            'date_screening' => 'required|date|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->first()], 422);
        }

        OrderDetail::create([
            'order_id' => $request->order_id,
            'movie_schedule_id' => $request->movie_schedule_id,
            'seat_id' => $request->seat_id,
            'date_screening' => $request->date_screening
        ]);

        return response(['message' => 'Cinema Ticket Successfully Purchased!'], 201);
    }
}
