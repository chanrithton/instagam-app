<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
 
    // create function Register Users
    public function register(Request $request){
        $vaildateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        // in crypt password
        // vaildateData  add new field and modify password use bcrypt is go thing that you don't understand
        $vaildateData['password'] = bcrypt($request->password);
        // check if the user  has image
        if($request->hasFile('profile_url')){
            $image = $request->file('profile_url');
            $name  = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/profile');
            $image->move($destinationPath, $name);
            $vaildateData['profile_url'] = $name;
        }
        // if($request->hasFile('profile_url')){
        // all this in folder storage in public folder
        //     // get the file name with extension
        //     $filenameWithExt = $request->file('profile_url')->getClientOriginalExtension();
        //     // get just the file name
        //     $filename = pathinfo($filenameWithExt,PATHINFO_FILENAME);
        //     // get just the extension
        //     $extension = $request->file('profile_url')->getClientOriginalExtension();
        //     // file name to store
        //     $fillNameToStore = $filename.'_'.time().'.'.$extension;
        //     // upload the image
        //     $path = $request->file('profile_url')->storeAs('public/profile_url', $fillNameToStore);
        //     $vaildateData['profile_url'] = $fillNameToStore;
            
        // }
        $user = User::create($vaildateData);  
        
        $accessToken = $user->createToken('authToken')->accessToken; // User access request key inorder get Data

        return response(['user' => $user, 'access_token' => $accessToken]);
        
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // get current data 
        $user = User::where('email',request('email'))->first();
        $userResponse = [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email
        ];
        return response()->json([
            'user' => $userResponse,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
        //return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
