<?php

namespace DC\Router\Authorization;

interface AuthorizerInterface {
    /**
     * @param $roles string[] A list of roles the user can have
     * @return bool
     */
    function isUserInRole($roles);

    /**
     * @return bool
     */
    function isUserLoggedIn();

    /**
     * @param \DC\Router\IRequest $request The request that was denied
     * @param string[] $requiredRoles
     * @return \DC\Router\IResponse
     */
    function showForbiddenMessage(\DC\Router\IRequest $request, array $requiredRoles);
}