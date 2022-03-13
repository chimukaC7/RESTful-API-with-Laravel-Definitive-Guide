<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        //return response()->json(['error' => $message, 'code' => $code], $code);
        return response()->json(['status_code' => $code, 'message' => $message], $code);
    }

    /*
     * So, basically for the methods that return a list of instances, we are going to use the "showAll" and
       for the methods that return a specific instance, we are going to use the "showOne".
      -Remember that the "showAll" and "showOne" methods use the 200 code by default.
     * */

//    protected function showAll(Collection $collection, $code = 200){
//        return $this->successResponse(['data' => $collection], $code);
//    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {//since we are using the first element, what happens if it is empty
            return $this->successResponse(['data' => $collection], $code);
        }

        $transformer = $collection->first()->transformer;//we can obtain the transformer for whatever position,first,last or middle
        //so we obtain the transformer based on the first element of the collection

        $collection = $this->filterData($collection, $transformer);
        //$collection = $this->sortData($collection);//sorting by the original model attributes
        $collection = $this->sortData($collection, $transformer);//sorting by the transformation attributes
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);//transform the collection
        $collection = $this->cacheResponse($collection);

        return $this->successResponse($collection, $code);
    }

    //changed from $modal to $instance
    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;

        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);//retrieving the attribute

            if (isset($attribute, $value)) {
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;
    }

    //Sorting results by any attribute
    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            //$attribute = request()->sort_by;//sorting using the original model's method
            $attribute = $transformer::originalAttribute(request()->sort_by);//sorting using the transformed attributes

            $collection = $collection->sortBy->{$attribute};
        }

        return $collection;
    }

    //can be used independently
    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50',//restricting the min and max page size
        ];

        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();//current page

        //PerPage----------------------------------------------
        $perPage = 15;
        if (request()->has('per_page')) {//allowing custom page size
            $perPage = (int) request()->per_page;//change the default value for a different value
        }
        //PerPage----------------------------------------------

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();//dividing the collection using the slice

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]);

        $paginated->appends(request()->all());//include the other request parameters

        return $paginated;

    }

    //method receives the data to transform and the respective transformer to be used
    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();//differentiate one request from another
        $queryParams = request()->query();//taking into account the url params

        ksort($queryParams);//sort the query params

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";

        //return Cache::remember($fullUrl, 30, function() use($data) {//for Laravel 5.8 and higher, the expiration time is in seconds
        return Cache::remember($fullUrl, 30 / 60, function () use ($data) {//for laravel 5.8 and lower
            return $data;
        });
    }
}