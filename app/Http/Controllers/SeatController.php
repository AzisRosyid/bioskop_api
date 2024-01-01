<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SeatController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $user = JWTAuth::user();

        if (!$user) {
            return response(['errors' => 'User not found'], 404);
        }

        $rules = [
            'movie_schedule_id' => 'required|integer|min:0',
            'screening_date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->first()], 422);
        }

        $seatIds = DB::table('movies as m')
        ->join('movie_schedules as ms', 'm.id', '=', 'ms.movie_id')
        ->join('cinemas as c', 'ms.cinema_id', '=', 'c.id')
        ->join('seats as s', 'c.id', '=', 's.cinema_id')
        ->where('ms.id', '=', $request->movie_schedule_id)
        ->groupBy('s.id')
        ->orderBy('s.id')
        ->pluck('s.id')
        ->toArray();

        $booked = DB::table('orders as o')
        ->join('order_details as d', 'o.id', '=', 'd.order_id')
        ->join('seats as s', 's.id', '=', 'd.seat_id')
        ->join('movie_schedules as ms', 'd.movie_schedule_id', '=', 'ms.id')
        ->where('ms.id', '=', $request->movie_schedule_id)
        ->where('d.date_screening', '=', $request->screening_date)
        ->groupBy('s.id')
        ->orderBy('s.id')
        ->pluck('s.id')
        ->toArray();

        $ordered = DB::table('orders as o')
        ->join('order_details as d', 'o.id', '=', 'd.order_id')
        ->join('seats as s', 's.id', '=', 'd.seat_id')
        ->join('movie_schedules as ms', 'd.movie_schedule_id', '=', 'ms.id')
        ->where('ms.id', '=', $request->movie_schedule_id)
        ->where('d.date_screening', '=', $request->screening_date)
        ->where('o.user_id', '=', $user->id)
        ->groupBy('s.id')
        ->orderBy('s.id')
        ->pluck('s.id')
        ->toArray();

        $seats = [];

        foreach ($seatIds as $seatId) {
            $status = 'Available';

            if (in_array($seatId, $booked)) {
                $status = 'Booked';
            }

            if (in_array($seatId, $ordered)) {
                $status = 'Ordered';
            }

            $seats[] = [
                'id' => $seatId,
                'status' => $status,
            ];
        }

        return response()->json(['seats' => $seats], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
