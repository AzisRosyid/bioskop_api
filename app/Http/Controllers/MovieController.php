<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\MovieSchedule;
use Illuminate\Support\Carbon;
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

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'duration' => 'required|date_format:H:i:s',
            'release_date' => 'required|date',
            'genre' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ];

        $messages = [
            'required' => 'The :attribute required'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()){
            return response(['errors' => $validator->errors()->first()], 422);
        }

        $file = null;
        if($request->file('image') != null){
            $photo = $request->file('image')->getClientOriginalExtension();
            $file = Carbon::now()->format('Y_m_d_His').'_'.$request->name.'.'.$photo;
            $request->file('image')->move('images', $file);
        }

        Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'release_date' => $request->release_date,
            'genre' => $request->genre,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $file
        ]);

        return response(['message' => 'Movie successfuly created!'], 201);
    }
}
