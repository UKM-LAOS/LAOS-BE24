<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\API\Compro\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'occupation' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'custom_fields' => json_encode([
                'occupation' => $request->occupation,
            ]),
        ]);

        return ResponseFormatter::success($user, 'User Registered');
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
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return ResponseFormatter::error('Unauthorized', 401);
        }

        $success = $this->respondWithToken($token);

        return ResponseFormatter::success($success, 'User Logged In');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $success = auth('api')->user();

        return ResponseFormatter::success($success, 'User Profile Retrieved');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth('api')->user()->id,
            'occupation' => 'required',
            'password' => 'nullable',
            'avatar' => 'nullable'
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors(), 422);
        }
        $imageName = null;
        $user = User::find(auth('api')->user()->id);

        if ($request->avatar) {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $image_64 = $request->avatar; //your base64 encoded data

            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);

            // find substring fro replace here eg: data:image/png;base64,

            $image = str_replace($replace, '', $image_64);

            $image = str_replace(' ', '+', $image);

            $imageName = Str::random(10) . '.' . $extension;

            Storage::disk('public')->put('avatars/' . $imageName, base64_decode($image));
        }

        if ($request->password) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'custom_fields' => json_encode([
                'occupation' => $request->occupation,
            ]),
            'avatar_url' => 'avatars/' . $imageName ?? $user->avatar,
        ]);

        return ResponseFormatter::success($user, 'Profile Updated');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return ResponseFormatter::success(null, 'User Logged Out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $success = $this->respondWithToken(auth('api')->refresh());

        return ResponseFormatter::success($success, 'Token Refreshed');
    }
}
