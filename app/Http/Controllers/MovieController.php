<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MovieSchedule;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.authenticate');
    }

    public function index()
    {
        $movies = Movie::all();
        return response()->json(['movies' => $movies], 200);
    }

    public function show($id)
    {
        $rules = [
            'movie_id' => 'required|integer|min:0',
        ];

        $validator = Validator::make(['movie_id' => $id], $rules);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->first()], 422);
        }

        $movie = Movie::find($id);

        if (!$movie) {
            return response(['errors' => 'Movie not found'], 404);
        }

        $schedules = MovieSchedule::where('movie_id', $id)->get();

        return response()->json(['movie' => $movie, 'schedules' => $schedules], 200);
    }
}
