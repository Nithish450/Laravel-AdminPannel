<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class InsertData extends Controller
{
    public function index() {
        return view('welcome');
    }

    public function add(Request $request) {
        $input = $request->all();
        $user = new User;
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->age = $input['age'];
        $user->password = bcrypt($input['password']);
        $user->save();

        // Redirect to the list page after saving the user
        return redirect()->route('layouts.listemployeelayout')->with('success', 'User added successfully!');
    }

    public function listUsers() {
        $users = User::all(); // Fetch all users
        return view('layouts.listemployeelayout', compact('users')); // Pass the users data to the view
    }

    // Method to show the edit form with existing user data
    public function edit($id) {
        $user = User::findOrFail($id); // Fetch user by ID
        return view('layouts.update', compact('user')); // Pass the user data to the edit view
    }

    // Method to update the user details
    public function update(Request $request, $id) {
        $input = $request->all();
        $user = User::findOrFail($id); // Fetch user by ID
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->age = $input['age'];
        
      
        
        $user->save();

        // Redirect back to the list page with success message
        return redirect()->route('layouts.listemployeelayout')->with('success', 'User updated successfully!');
    }
    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
    
            // Move user to trash
            \DB::table('user_trash')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'age' => $user->age,
                'deleted_at' => now(), // Current timestamp
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]);
    
            // Delete user from the original table
            $user->delete();
    
            // Return JSON response for AJAX
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
    
            // Return JSON response for AJAX
            return response()->json(['success' => false]);
        }
    }
    public function restore($id)
    {
        $trashedUser = \DB::table('user_trash')->where('id', $id)->first();
    
        if ($trashedUser) {
            // Create a new user with the details from trash
            $user = new User();
            $user->name = $trashedUser->name;
            $user->email = $trashedUser->email;
            $user->age = $trashedUser->age;
    
            // Optionally, set a default password or handle password differently if needed
            $user->password = bcrypt('default_password'); // Adjust if password is required
    
            $user->created_at = $trashedUser->created_at;
            $user->updated_at = $trashedUser->updated_at;
    
            $user->save(); // Save the new user to the original table
    
            // Remove the user from the trash
            \DB::table('user_trash')->where('id', $id)->delete();
    
            return redirect()->back()->with('success', 'User restored successfully.');
        }
        
        return redirect()->back()->with('error', 'User not found.');
    }
    

    

    public function deletePermenant($id)
    {
        $user = \DB::table('user_trash')->where('id', $id)->first();
        if ($user) {
            \DB::table('user_trash')->where('id', $id)->delete();
            return redirect()->back()->with('success', 'User deleted permanently.');
        }
        return redirect()->back()->with('error', 'User not found.');
    }
    


public function showTrash()
{
    $trashedUsers = \DB::table('user_trash')->get(); // Fetch all trashed users
    return view('trash', compact('trashedUsers')); // Pass the trashed users to the view
}

}
