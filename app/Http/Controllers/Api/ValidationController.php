<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    /**
     * Check if email is available for registration
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $email = $request->email;
        
        // Block admin@gmail.com specifically
        if (strtolower($email) === 'admin@gmail.com') {
            return response()->json([
                'available' => false,
                'message' => 'Email tidak tersedia'
            ]);
        }
        
        // Check if email already exists
        $exists = User::where('email', $email)->exists();
        
        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => 'Email sudah terdaftar'
            ]);
        }
        
        // Email is available
        return response()->json([
            'available' => true,
            'message' => 'Email tersedia'
        ]);
    }
    
    /**
     * Validate password strength
     */
    public function validatePassword(Request $request)
    {
        
        $request->validate([
            'password' => 'required|string'
        ]);
        
        $password = $request->password;
        
        // Simple validation checks
        $messages = [];
        $isValid = true;
        
        if (strlen($password) < 8) {
            $messages[] = 'Minimal 8 karakter';
            $isValid = false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            $messages[] = 'Harus ada huruf kecil';
            $isValid = false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $messages[] = 'Harus ada huruf besar';
            $isValid = false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            $messages[] = 'Harus ada angka';
            $isValid = false;
        }
        
        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Password memenuhi syarat' : implode(', ', $messages)
        ]);
    }
    
    /**
     * Check login credentials
     */
    public function checkLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        
        $email = $request->email;
        $password = $request->password;
        
        if (!$email || !$password) {
            return response()->json([
                'valid' => false,
                'message' => 'Email dan password harus diisi'
            ]);
        }
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'Email tidak terdaftar'
            ]);
        }
        
        // Check if account is active
        if (!$user->is_active) {
            return response()->json([
                'valid' => false,
                'message' => 'Akun Anda telah dinonaktifkan'
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'message' => 'Kredensial valid'
        ]);
    }
}