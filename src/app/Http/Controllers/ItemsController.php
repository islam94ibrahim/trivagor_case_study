<?php

namespace App\Http\Controllers;

use App\Filters\ItemLocationCityFilter;
use App\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Crell\ApiProblem\ApiProblem;

class ItemsController extends BaseController
{
    /**
     * Get all items for the current user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $items = QueryBuilder::for(Item::class, $request)
            ->owned(Auth::user()->id)
            ->allowedFilters([
                'reputation_badge',
                'category',
                'rating',
                AllowedFilter::custom('city', new ItemLocationCityFilter),
                AllowedFilter::scope('min_availability'),
                AllowedFilter::scope('max_availability')
            ])
            ->get();

        return response()->json($items);
    }

    /**
     * Get user item by the specified id.
     *
     * @param string $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $itemId)
    {
        $item = $this->getItem($itemId);

        return ($item instanceof Item ? response()->json($item) : $item);
    }

    /**
     * Create a new item from the given inputs.
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

        $item = Auth::user()->items()->create($request->all());

        return response()->json($item);
    }

    /**
     * Update user item by the specified id from the given inputs.
     *
     * @param Request $request
     * @param string $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $itemId)
    {
        $item = $this->getItem($itemId);

        if (!$item instanceof Item) {
            return $item;
        }

        if (!Gate::allows('manipulate-items', $item)) {
            $error = new ApiProblem('Permission error.');
            $error->setDetail('You do not have permission to update this item.');

            return response($error->asJson(), 401)
                ->header('Content-Type', 'application/problem+json');
        }

        $validation = $this->validateRequest($request);
        if ($validation !== true) {
            return $validation;
        }

        return tap($item)->update($request->all());
    }

    /**
     * Delete user item by the given id.
     *
     * @param string $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $itemId)
    {
        $item = $this->getItem($itemId);

        if (!$item instanceof Item) {
            return $item;
        }

        if (!Gate::allows('manipulate-items', $item)) {
            $error = new ApiProblem('Permission error.');
            $error->setDetail('You do not have permission to update this item.');

            return response($error->asJson(), 401)
                ->header('Content-Type', 'application/problem+json');
        }

        $item->delete();

        return response()->json(['Item deleted successfully.']);
    }

    public function book(string $itemId)
    {
        $item = $this->getItem($itemId);

        if (!$item instanceof Item) {
            return $item;
        }

        if ($item->availability === 0) {
            $error = new ApiProblem('Item availability error.');
            $error->setDetail('There is no availability left for this item.');

            return response($error->asJson(), 400)
                ->header('Content-Type', 'application/problem+json');
        }

        $item->availability = $item->availability - 1;
        $item->save();

        return response()->json(['Item booked successfully.']);
    }

    private function getItem(string $itemId)
    {
        $item = Item::find($itemId);

        if (empty($item)) {
            $error = new ApiProblem('Item not found.');
            $error->setDetail('The item that you requested does not exist.');

            return response($error->asJson(), 404)
                ->header('Content-Type', 'application/problem+json');
        }

        return $item;
    }

    private function validateRequest(Request $request)
    {
        try {
            $this->validate($request, [
                'location_id' => 'required|exists:App\Location,id',
                'name' => 'required|string|min:11|not_contains:free,offer,book,website',
                'rating' => 'required|numeric|min:0|max:5',
                'category' => 'required|string|in:hotel,alternative,hostel,lodge,resort,guest-house',
                'image' => 'required|url',
                'reputation' => 'required|numeric|min:0|max:1000',
                'price' => 'required|numeric',
                'availability' => 'required|numeric'
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
