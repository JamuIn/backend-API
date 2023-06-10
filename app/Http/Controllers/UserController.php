<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $user = User::find(auth()->user()->id);
        $role = $user->getRoleNames();

        if ($role[0] != 'admin') {
            return response()->json([
                'data' => $users->makeHidden(['email', 'phone_number', 'address', 'email_verified_at'])
            ], 200);
        }
        return response()->json($users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (auth()->user()->id != $user->id) {
            return response()->json([
                'user' => $user->makeHidden(['email', 'phone_number', 'address', 'email_verified_at'])
            ], 200);
        }

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!auth()->user()->id == $user->id) {
            return response()->json(['message' => 'You are not allowed to edit this user'], 401);
        }

        $user->full_name = $request->input('full_name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->address = $request->input('address');

        // Handle profile image upload if present
        if ($request->hasFile('image')) {
            // delete old image
            if ($user->profile_img != '') {
                $old_image = substr(strrchr($user->profile_img, "/"), 1);
                $this->deleteImageFile($old_image);
            }

            $imageName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/users/profile_images/');
            $request->file('image')->move($destinationPath, $imageName);

            $image_url = asset('assets/users/profile_images/' . $imageName);
            $user->profile_img = $image_url;

            $user->save();
        }

        $user->save();

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $image = substr(strrchr($user->profile_img, "/"), 1);
        $this->deleteImageFile($image);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    protected function deleteImageFile($fileName)
    {
        $filePath = public_path('assets/users/profile_images/' . $fileName);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
