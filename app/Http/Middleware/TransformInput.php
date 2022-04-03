<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $transformedInput = [];
        $allFields = $request->all();//obtain the params in the body of the request
        $queryParams = $request->query();
        $transformableFields = array_diff($allFields, $queryParams);

        foreach ($transformableFields as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;//obtaining the original key name
        }

        $request->replace($transformedInput);
        $response = $next($request);

        //mapping the validation response to the transformed key names
        if (isset($response->exception) && $response->exception instanceof ValidationException) {//is error a validation exception
            $data = $response->getData();//get data from the response

            $transformedErrors = [];

            foreach ($data->error as $field => $error) {//loop over every error
                $transformedField = $transformer::transformedAttribute($field);//get the transformed name of the field
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);//replacing the
            }

            $data->error = $transformedErrors;

            $response->setData($data);
        }
        return $response;
    }
}
