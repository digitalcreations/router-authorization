<?php

namespace DC\Router\Authorization;

class AuthorizationFilter implements \DC\Router\IGlobalFilter {

    const TAG_AUTHORIZE = "authorize";

    /**
     * @var AuthorizerInterface
     */
    private $authenticator;
    /**
     * @var Reflector
     */
    private $reflector;

    function __construct(AuthorizerInterface $authenticator)
    {
        $this->authenticator = $authenticator;
        $this->reflector = new Reflector();

        \phpDocumentor\Reflection\DocBlock\Tag::registerTagHandler(self::TAG_AUTHORIZE, '\DC\Router\Authorization\AuthorizeTag');
    }


    /**
     * @inheritdoc
     */
    function beforeRouteExecuting(\DC\Router\IRequest $request, \DC\Router\IRoute $route, array $params, array $rawParams)
    {
        $callable = $route->getCallable();
        $reflection = $this->reflector->getReflectionFunctionForCallable($callable);
        $docBlock = new \phpDocumentor\Reflection\DocBlock($reflection);
        /** @var \DC\Router\Authorization\AuthorizeTag[] $tags */
        $tags = $docBlock->getTagsByName(self::TAG_AUTHORIZE);

        if (count($tags) > 0) {
            $roles = $tags[0]->getRoles();
            if (!$this->authenticator->isUserLoggedIn()
                || (count($roles) > 0 && !$this->authenticator->isUserInRole($roles))) {
                return $this->authenticator->showForbiddenMessage($request, $roles);
            }
        }
    }


    //region Empty methods
    /**
     * @inheritdoc
     */
    function routeExecuting(\DC\Router\IRequest $request, \DC\Router\IRoute $route, array $params, array $rawParams) { }

    /**
     * @inheritdoc
     */
    function afterRouteExecuting(\DC\Router\IRequest $request, \DC\Router\IRoute $route, array $params, array $rawParams, \DC\Router\IResponse $response) { }

    /**
     * @inheritdoc
     */
    function afterRouteExecuted(\DC\Router\IRequest $request, \DC\Router\IRoute $route, array $params, array $rawParams, \DC\Router\IResponse $response) { }
    //endregion
}