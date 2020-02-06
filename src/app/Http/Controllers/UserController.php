<?php

namespace App\Http\Controllers;

use App\User;
use Crell\ApiProblem\ApiProblem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * Create a new user instances.
     *
     * @param  Request  $request
     * @return \App\User
     */
    public function create(Request $request)
    {
        try {
            $user = User::forceCreate([
                'username' => $request->username,
                'api_token' => Str::random(80),
            ]);
        } catch (\Exception $e) {
            $error = new ApiProblem(
                (preg_match('/\\\\([\w]+)$/', get_class($e), $matches)) ?
                    $matches[1] :
                    get_class($e)
            );
            $error->setDetail($e->getMessage());

            return response($error->asJson(), 422)
                ->header('Content-Type', 'application/problem+json');
        }

        return response()->json($user);
    }
}
