<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

class HandleApiExceptions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            //code...
            return $next($request);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Vous n\'avez pas la permission d\'accéder à cette ressource.', 'error' => $e
            ], 403);
        } catch (AuthenticationException $e) {
            return response()->json([
                'message' => 'Non authentifié.', 'error' => $e
            ], 401);
        } catch (HttpException $e) {
            return response()->json([
                'message' => 'Erreur interne du serveur.', 'error' => $e
            ], $e->getStatusCode());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur inattendue.', 'error' => $e
            ], 500);
        }
    }
}
