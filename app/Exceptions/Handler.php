<?php

namespace App\Exceptions;

use Exception;//use Throwable; // add this line
use App\Traits\ApiResponser;
use Asm89\Stack\CorsService;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/*
 * use Throwable; // add this line

public function report(Throwable $exception); // replace Exception with Throwable
public function render($request, Throwable $exception); // replace Exception with Throwable
 * */

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        OAuthServerException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)// replace Exception with Throwable
    {
        //the "report" method is executed every time to report in the log file the exceptions.
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     * is executed everytime when an exception is thrown
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)// replace Exception with Throwable
    {
        //return parent::render($request,$exception);

        $response = $this->handleException($request, $exception);

        app(CorsService::class)->addActualRequestHeaders($response, $request);

        return $response;

    }

    //returning HTML and redirecting when required
    private function isFrontend($request)
    {
        //check if request are coming from the web
        //it is frontend if the request accepts HTML and/or the request is a web request
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    public function handleException($request, Exception $exception)// replace Exception with Throwable
    {
        //ensure exceptions are sent as json
        //Create a response object from the given validation exception.
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();

            if ($this->isFrontend($request)) {
                //if an ajax request or frontend request
                return $request->ajax()
                    ? response()->json($errors, 422)
                    : redirect()->back()->withInput($request->input())->withErrors($errors);//redirect back with the inputs and list of errors
            }

            return $this->errorResponse($errors, 422);
        }

        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse("Does not exists any {$modelName} with the specified identification", 404);
        }

        //* Convert an authentication exception into an unauthenticated response.
        if ($exception instanceof AuthenticationException) {

            if ($this->isFrontend($request)) {//if frontend redirect back to login page
                return redirect()->guest('login');
            }

            return $this->errorResponse('Unauthenticated', 401);
        }

        //authorization is permission related
        //we have an authenticated user but that specific user does not have permission to
        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), 403);//unauthorized
        }

        // if a user send a request to a specific route that already
        //exist, but with the wrong HTTP method. For example, you send a request to buyers endpoint.
        if ($exception instanceof MethodNotAllowedHttpException) {//Wrong HTTP verb for the URL
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }

        if ($exception instanceof NotFoundHttpException) {//URL related
            return $this->errorResponse('The specified URL cannot be found', 404);
        }

        if ($exception instanceof HttpException) {//General HTTP exception
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        //handles the foreign key constraint violation during an update but more importantly duly a delete
        if ($exception instanceof QueryException) {//Query exception
            //$errorCode = $exception->errorInfo[1];

            if ($exception->errorInfo[1] == 1451) {//we are only handling errors related to this code
                return $this->errorResponse('Cannot remove this resource permanently. It is related with another resource', 409);
            }
        }

        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }


        //Handling unexpected exceptions,
        if (config('app.debug')) {//returning the detail of unexpected exception during debug mode
            return parent::render($request, $exception);//returning a detailed error
        }

        //Handling unexpected exceptions
        return $this->errorResponse('Unexpected Exception. Try again later', 500);
    }

}
