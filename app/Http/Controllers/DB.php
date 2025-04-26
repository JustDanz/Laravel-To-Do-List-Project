<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function showCalendar()
    {
        // Query to group tasks by day_of_week and concatenate other fields
        $tasksByDay = DB::table('tasks')
            ->select('day_of_week', 
                     DB::raw('GROUP_CONCAT(id) as ids'), 
                     DB::raw('GROUP_CONCAT(title) as titles'),
                     DB::raw('GROUP_CONCAT(description) as descriptions')) // You can add more columns
            ->groupBy('day_of_week')
            ->get();

        // Convert the query result to a more manageable format
        $tasks = [];
        foreach ($tasksByDay as $taskDay) {
            $tasks[$taskDay->day_of_week] = [
                'ids' => explode(',', $taskDay->ids),
                'titles' => explode(',', $taskDay->titles),
                'descriptions' => explode(',', $taskDay->descriptions),
            ];
        }

        // Pass the grouped tasks to the view
        return view('calendar', compact('tasks'));
    }
}
