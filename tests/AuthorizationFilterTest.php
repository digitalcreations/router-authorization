<?php

class AuthorizationFilterTest extends PHPUnit_Framework_TestCase {
    function testBeforeRouteExecutingUserNotInRoleReturnsErrorPage() {
        $mockRequest = $this->getMock('\DC\Router\IRequest');
        $mockRoute = $this->getMock('\DC\Router\IRoute');
        $mockRoute
            ->expects($this->once())
            ->method('getCallable')
            ->willReturn(
            /**
             * @authorize admin
             */
                function() {});

        $desiredResponse = new \DC\Router\Response();

        $mockAuthorizer = $this->getMock('\DC\Router\Authorization\AuthorizerInterface');
        $mockAuthorizer
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true);
        $mockAuthorizer
            ->expects($this->once())
            ->method('isUserInRole')
            ->with($this->equalTo(['admin']))
            ->willReturn(false);
        $mockAuthorizer
            ->expects($this->once())
            ->method('showForbiddenMessage')
            ->willReturn($desiredResponse);

        $filter = new \DC\Router\Authorization\AuthorizationFilter($mockAuthorizer);
        $response = $filter->beforeRouteExecuting($mockRequest, $mockRoute, [], []);
        $this->assertEquals($desiredResponse, $response);
    }

    function testBeforeRouteExecutingUserInRoleReturnsNull() {
        $mockRequest = $this->getMock('\DC\Router\IRequest');
        $mockRoute = $this->getMock('\DC\Router\IRoute');
        $mockRoute
            ->expects($this->once())
            ->method('getCallable')
            ->willReturn(
            /**
             * @authorize admin accounting
             */
                function() {});

        $mockAuthorizer = $this->getMock('\DC\Router\Authorization\AuthorizerInterface');
        $mockAuthorizer
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true);
        $mockAuthorizer
            ->expects($this->once())
            ->method('isUserInRole')
            ->with($this->equalTo(['admin', 'accounting']))
            ->willReturn(true);

        $filter = new \DC\Router\Authorization\AuthorizationFilter($mockAuthorizer);
        $response = $filter->beforeRouteExecuting($mockRequest, $mockRoute, [], []);
        $this->assertNull($response);
    }

    function testBeforeRouteRequireLoggedInIsLoggedIn() {
        $mockRequest = $this->getMock('\DC\Router\IRequest');
        $mockRoute = $this->getMock('\DC\Router\IRoute');
        $mockRoute
            ->expects($this->once())
            ->method('getCallable')
            ->willReturn(
            /**
             * @authorize
             */
                function() {});

        $mockAuthorizer = $this->getMock('\DC\Router\Authorization\AuthorizerInterface');
        $mockAuthorizer
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true);

        $filter = new \DC\Router\Authorization\AuthorizationFilter($mockAuthorizer);
        $response = $filter->beforeRouteExecuting($mockRequest, $mockRoute, [], []);
        $this->assertNull($response);
    }
}
 