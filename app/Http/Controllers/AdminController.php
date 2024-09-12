<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::where('role', 'admin')->orWhere('role', 'superadmin')->get();
        return view('admins.index', compact('admins'));
    }
    public function register(Request $request)
    {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:admins,name',
                'email' => 'required|string|email|max:255|unique:admins,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|in:admin,superadmin',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Create the new admin if validation passes
            $createNewAdmin = new CreateNewAdmin();
            $createNewAdmin->create($request->all());

            // Redirect to the admins.index route with a success message
            return redirect()->route('admins.index')->with('register-success', 'Superadmin/Admin Registered successfully!');
        }
        public function destroy(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        // Check if the admin is not a superadmin
        if ($admin->role === 'superadmin') {
            return redirect()->route('admins.index')->with('admin-error', 'Superadmins cannot be deleted.');
        }

        // Verify the password
        if (!Hash::check($request->input('password'), Auth::user()->password)) {
            return redirect()->route('admins.index')->with('delete-error', 'Incorrect password. Unable to delete admin.');
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('delete-success', 'Admin deleted successfully!');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withErrors(['email' => __($status)]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
    }
}
