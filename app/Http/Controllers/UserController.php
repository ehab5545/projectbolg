<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Users; 

use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function register(Request $request)
    {
    
        
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Create a new user instance
            $input = $request->all();
            $input['password'] = Hash::make($input['password']); // Hash the password
            dd($input);
            Users::create($input); // Use the correct model name

            return redirect()->route('User.login')
                             ->with('success', 'User added successfully');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
   
        $request->validate([
            'username' => 'required',
            'email' => 'required',
            'password' => 'required', 
        ]);
  
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        Users::create($input); 
        return redirect()->route('login')
        ->with('success', 'User added successfully'); // Corrected 'succes' to 'success'
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Get the authenticated user
    
            return redirect()->route('posts.post')
                             ->with('success', 'User logged in successfully');
        }
        // Authentication failed
        return redirect()->route('login')->with('error', 'Invalid email or password');
    
    }
    /*
   
    public function index()
    {
        $users = User::all();
        return view('User.index', compact('users'));
    }


    public function show(User $user)
    {
        return view('User.show', compact('user'));
    }
*/
    /**
     * Show the form for editing the specified resource.
     */
 /*   public function edit(User $user)
    {
        return view('User.edit', compact('user'));
    }

 
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required',
            'Password' => 'required', // Consider renaming to 'password' for Laravel conventions
        ]);

        $input = $request->all();
        $user->update($input); // Assuming 'register' method is not standard, use 'update' instead
        return redirect()->route('User.index')
                         ->with('success', 'User updated successfully'); // Corrected 'succes' to 'success'
    }

   
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('User.index')
                         ->with('success', 'User deleted successfully'); 
    }
    */
}
