<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\JsonResponse;
use Override;

class LogoutResponse implements LogoutResponseContract
{
    #[Override]
    public function toResponse($request)
    {
        $redirect = $request->input('from') === 'admin'
        ? 'admin/login'
        : '/login';
        
        return $request->wantsJson()
        ? new JsonResponse('', 204)
        : redirect($redirect);
    }
}