<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Urutan hari yang konsisten
    private $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    // Menampilkan tugas dalam format Trello (group by status)
    public function index()
    {
        $tasks = [
            'belum_mulai' => Task::where('status', 'belum_mulai')->get(),
            'proses' => Task::where('status', 'proses')->get(),
            'selesai' => Task::where('status', 'selesai')->get()
        ];

        return view('index', compact('tasks'));
    }

    // Menampilkan kalender (group by day)
    public function calendar()
    {
        $tasks = Task::orderBy('day_of_week')
                   ->orderBy('created_at')
                   ->get()
                   ->groupBy('day_of_week');

        // Pastikan semua hari ada meski tanpa task
        foreach ($this->daysOfWeek as $day) {
            if (!isset($tasks[$day])) {
                $tasks[$day] = collect();
            }
        }

        return view('calendar', [
            'tasks' => $tasks,
            'daysOfWeek' => $this->daysOfWeek
        ]);
    }

    // Menyimpan task baru
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'
    ]);

    // Set default status
    $validated['status'] = 'belum_mulai';

    try {
        $task = Task::create($validated);
        return redirect()->route('home')->with('success', 'Task created successfully!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Failed to create task: ' . $e->getMessage());
    }
}
    // Mengupdate task
    public function update(Request $request, Task $task)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
        'status' => 'required|in:belum_mulai,proses,selesai'
    ]);

    $task->update($validated);

    // Return JSON response for AJAX requests
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully!',
            'task' => $task
        ]);
    }

    // Return redirect for regular form submissions
    return redirect()->route('home')->with('success', 'Task updated successfully!');
}

    // Menghapus task
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('home')->with('success', 'Task deleted successfully!');
    }

    // API: Update status task (untuk drag and drop)
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:belum_mulai,proses,selesai']);
        
        $task->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
    
    public function updateDay(Request $request, Task $task)
    {
        $request->validate(['day_of_week' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu']);
        
        $task->update(['day_of_week' => $request->day_of_week]);
        return response()->json(['success' => true]);
    }
}