<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($request->wantsJson() | strpos($request->getUri(), '/api/') !== false){
            return $this->customApiResponse($exception);
        }

        return parent::render($request, $exception);
    }


    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = 'Não autorizado';
                break;
            case 403:
                $response['message'] = 'Acesso restrito';
                break;
            case 404:
                $response['message'] = 'Nada encontrado';
                break;
            case 405:
                $response['message'] = 'Tipo de requisição não permitido';
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = ($statusCode == 500) ? 'Algo de errado ocorreu' : $exception->getMessage();
                break;
        }

        return response()->json($response, $statusCode);
    }


}
