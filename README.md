## Installation

```
$ composer require dc/router-authorization
```

Or add it to `composer.json`:

```json
"require": {
	"dc/router-authorization": "0.*"
}
```

```
$ composer install
```

## Using

You will need to implement a simple interface yourself. Here is a sample implementation:

```php
class Authorizer implements \DC\Router\Authorization\AuthorizerInterface {
    /**
     * @param $roles string[] A list of roles the user can have
     * @return bool
     */
    function isUserInRole($roles) {
        // your custom logic goes here:
        return \UserService::IsInRole($roles);
    }

    /**
     * @return bool
     */
    function isUserLoggedIn() {
        // imagine this is better:
        return isset($_SESSION['user_id']);
    }

    /**
     * @param \DC\Router\IRequest $request The request that was denied
     * @param string[] $requiredRoles
     * @return \DC\Router\IResponse
     */
    function showForbiddenMessage(\DC\Router\IRequest $request, array $requiredRoles) {
		$response = new \DC\Router\Response();
		$response->setStatusCode(\DC\Router\StatusCodes::HTTP_FORBIDDEN);
		$response->setContent("Don't try that again");
		return $response;
    }
}
```

Before calling `\DC\Router\IoC\RouterSetup::setup()`, you need to register the `AuthorizationFilter` class and your customer authorizer implementation:

```php
$container
  ->register('\DC\Router\Authorization\AuthorizationFilter')
  ->to('\DC\Router\IGlobalFilter');
$container
  ->register('\Authorizer')
  ->to('\DC\Router\Authorization\AuthorizerInterface');

\DC\Router\IoC\RouterSetup::setup($container);
```

When this is done, you can decorate any routes in your controllers like this:

```php
class KittenController extends \DC\Router\ControllerBase {

  /**
   * @route GET /kittens
   */  
  function getAll() {
    // this route is open for everyone
    return [
		// making kittens is left as an exercise for the reader
	];
  }

  /**
   * @route GET /kitten/{id:int}
   * @authorize
   */  
  function getKitten($id) {
    // you will have to be logged in to access kitten details
    return new Kitten($id);
  }

  /**
   * @route POST /kitten
   * @authorize admin
   */
  function newKitten() {
    // you will have to be in the admin role to post new kittens
    ORM::Save(new Kitten());
  }
}
```

## `@authorize`

The `@authorize` tag works in two ways:

- By adding it to a route without parameters, only logged in users will be able to access it. See `\DC\Router\Authorization\AuthorizerInterface::isUserLoggedIn`.
- By writing one or more role names after it, a user will have to be logged in to access the route, and it will have to be in at least one of the roles specified.  See `\DC\Router\Authorization\AuthorizerInterface::isUserInRole`.