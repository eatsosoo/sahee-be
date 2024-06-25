<?php

namespace App\Http\Middleware;

use App\Helpers\Responses\ApiResponse;
use App\Helpers\Responses\HttpStatuses;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Closure;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthGate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permissionId)
    {
        if (config('app.auth_gate') === false) {
            return $next($request);
        }

        try {
            if (!Auth::check()) {
                return $this->sendForbiddenResponse();
            }
            $user = Auth::user();
            $roleId = $user->role;
            $rolePermission = RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->first();
            Log::debug($rolePermission);

            if (!$rolePermission) {
                return $this->sendForbiddenResponse();
            }

            return $next($request);
        } catch (Exception $e) {
            Log::error('Authentication failed: ' . $e->getMessage());
            // Unauthorized access
            return $this->sendForbiddenResponse();
        }
    }

    /**
     * sendForbiddenResponse
     *
     * @return Response
     */
    private function sendForbiddenResponse()
    {
        return ApiResponse::v1()
            ->withStatusCode(HttpStatuses::HTTP_FORBIDDEN)
            ->fail(
                [
                    'code' => HttpStatuses::HTTP_FORBIDDEN,
                    'message' => 'Forbidden access'
                ],
                'errors'
            );
    }
}
