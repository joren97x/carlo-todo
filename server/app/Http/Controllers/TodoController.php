<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        return response(['todos' => Todo::with('user')->where('user_id', $request->user()->id)->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'user_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'level' => 'required'
        ]);

        Todo::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level
        ]);

        return response(['message' => 'Todo Created Successfully']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        
        return response(['todo' => Todo::with('user')->find($id)]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'level' => 'required'
        ]);

        $todo = Todo::find($id);
        $todo->name = $request->name;
        $todo->description = $request->description;
        $todo->level = $request->level;

        $todo->update();

        return response(['message' => 'Todo Updated Successfully']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $todo = Todo::find($id);
        $todo->delete();
        return response(['message' => 'Todo Deleted Successfully']);
    }

    public function update_status(string $id) {
        $todo = Todo::find($id);
        $todo->status = !$todo->status;
        $todo->update();
        return response(['message' => 'Todo Status Updated Successfully']);
    }

}
