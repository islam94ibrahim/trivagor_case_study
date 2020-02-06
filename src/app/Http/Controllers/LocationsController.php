<?php

namespace App\Http\Controllers;

use App\Location;
use Crell\ApiProblem\ApiProblem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class LocationsController extends BaseController
{
    /**
     * Get all locations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Location::all());
    }

    /**
     * Get location by the specified id.
     *
     * @param string $locationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $locationId)
    {
        $location = $this->getLocation($locationId);

        return ($location instanceof Location ? response()->json($location) : $location);
    }


    /**
     * Create a new location from the given inputs.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $validation = $this->validateRequest($request);
        if ($validation !== true) {
            return $validation;
        }

        $location = Location::create($request->all());

        return response()->json($location);
    }

    /**
     * Update location by the specified id from the given inputs.
     *
     * @param Request $request
     * @param string $locationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $locationId)
    {
        $location = $this->getLocation($locationId);

        if (!$location instanceof Location) {
            return $location;
        }

        $validation = $this->validateRequest($request);
        if ($validation !== true) {
            return $validation;
        }

        return tap($location)->update($request->all());
    }

    /**
     * Delete location by the specified id.
     *
     * @param string $locationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $locationId)
    {
        $location = $this->getLocation($locationId);

        if (!$location instanceof Location) {
            return $location;
        }

        $location->delete();

        return response()->json(['Location deleted successfully']);
    }

    private function getLocation(string $locationId)
    {
        $location = Location::find($locationId);

        if (empty($location)) {
            $error = new ApiProblem('Location not found.');
            $error->setDetail('The location that you requested does not exist.');

            return response($error->asJson(), 404)
                ->header('Content-Type', 'application/problem+json');
        }

        return $location;
    }

    private function validateRequest(Request $request)
    {
        try {
            $this->validate($request, [
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                'zip_code' => 'required|digits:5',
                'address' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            $error = new ApiProblem('Validation error.');
            foreach ($e->errors() as $key => $value) {
                $error[$key] = $value;
            }

            return response($error->asJson(), 422)
                ->header('Content-Type', 'application/problem+json');
        }

        return true;
    }
}
