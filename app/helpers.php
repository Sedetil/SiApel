<?php

if (! function_exists('role_route_name')) {
    /**
     * Resolve the route name prefixed with the current authenticated user's role.
     */
    function role_route_name(string $name): string
    {
        $prefix = (auth()->check() && auth()->user()->isAdmin()) ? 'admin.' : 'timdis.';

        return $prefix.$name;
    }
}

if (! function_exists('role_route')) {
    /**
     * Generate the URL to a named route with role-based prefixing.
     */
    function role_route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route(role_route_name($name), $parameters, $absolute);
    }
}
