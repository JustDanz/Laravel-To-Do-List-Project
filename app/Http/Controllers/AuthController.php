<?php 

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;  // Ensure this is imported for Hash
use Illuminate\Support\Facades\Auth;  // Ensure this is imported for Auth

class AuthController extends Controller
{
    // Show Register Form
    public function showRegisterForm()
    {
        return view('register');
    }

    // Handle Register
    public function register(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user in the database
        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Redirect to login page after successful registration
        return redirect('/login')->with('success', 'Registration successful. Please login.');
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('login');
    }

    // Handle Login
    public function login(Request $request)
    {
        // Validate login inputs
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to login
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/');  // Redirect to dashboard on successful login
        }

        // Redirect back with an error message if login fails
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    // Index for tasks (assuming user is authenticated)
    public function index()
    {
        // Fetch tasks and group them by their status
        $tasks = Task::all()->groupBy('status');

        // Return view with tasks
        return view('tasks.index', compact('tasks'));
    }
    public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
}
}
