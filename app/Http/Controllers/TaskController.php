<?php

namespace App\Http\Controllers;

use App\Models\Task;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TaskController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Auth::user()->tasks()->orderByDesc('created_at')->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
        ]);

        $tasks= Auth::user()->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'pending', // Default status 'pending'
        ]);

        // if ($tasks) {
        //     Session::flash('status', 'Success');
        //     Session::flash('message', 'Add new Task success!');
        //     // Tambahkan log untuk memastikan data berhasil disimpan
        //     Log::info('New task created:', $tasks->toArray());
        // }
        // dd(Session::all()); // Memeriksa apakah session flash sudah ada

        // return redirect()->route('tasks.index');
        return redirect()->route('tasks.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('tasks.show', compact('task'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $task->description,
            'status' => $validated['status'] ?? $task->status,
        ]);

        if($task) {
            Session::flash('status', 'Success');
            Session::flash('message', 'Edit Task success!');
        }

        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
       
        DB::transaction(function () use ($task) {
            $task->delete();
        });

        if($task) {
            Session::flash('status', 'Success');
            Session::flash('message', 'Delete Task success!');
        }
        // dd(Session::all());

        return redirect()->route('tasks.index');
    }
}
