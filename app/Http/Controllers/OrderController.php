<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\MovieSchedule;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Collection;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }

    public function index()
    {
        try {
            $user = JWTAuth::user();

            if (!$user) {
                return response(['errors' => 'User not found'], 404);
            }

            $tickets = [];

            $orders = Order::where('user_id', $user->id)->get();

            foreach ($orders as $o) {
                $orderDetails = OrderDetail::where('order_id', $o->id)->get();
                foreach ($orderDetails as $d) {
                    $movieSchedule = MovieSchedule::find($d->movie_schedule_id);
                    if (!$movieSchedule) {
                        continue;
                    }

                    $movie = Movie::find($movieSchedule->movie_id);
                    $cinema = Cinema::find($movieSchedule->cinema_id);

                    $ticket = [
                        'movie_title' => $movie ? $movie->title : 'N/A',
                        'movie_price' => $movie ? $movie->price : 'N/A',
                        'movie_image' => $movie ? $movie->image : 'N/A',
                        'cinema' => $cinema ? $cinema->name : 'N/A',
                        'seat' => $d->seat_id,
                        'date' => $d->date_screening,
                        'start_time' => $movieSchedule->start_time,
                        'end_time' => $movieSchedule->end_time,
                        'status' => Carbon::now()->toDateString() > Carbon::parse($d->date_screening)->toDateString() ? 'Expired' : 'Valid',
                    ];

                    $tickets[] = $ticket;
                }
            }

            // Ticket was order by status ('Valid' on top, then 'Expired' bottom), then order by date, then order by start time
            $sortedTickets = (new Collection($tickets))->sortBy(function ($ticket) {
                return [$ticket['date'], $ticket['start_time']];
            })->sortByDesc('status')->values()->all();

            return response()->json(['tickets' => $sortedTickets], 200);
        } catch (\Exception $e) {
            return response(['errors' => $e->getMessage()], 500);
        }
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

    public function storeDetail(Request $request)
    {
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
