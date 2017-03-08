<?php
require('core/bootstrap.php');
use core\Bootstrap;

try {
    Bootstrap::init(function() {
        /**
         * You can init your database setup here
         * Everything is loaded at this point but not yet routed
         */
    });
}
catch (Exception $e) {
    /**
     * You can catch all exceptions here to either show them in your own view
     * or simply log everything somewhere and show an 404 page
     *
     * The following exceptions can be thrown from the core, catching a CoreException will catch
     * all the derived exceptions as well
     *
     * CoreException                Base exception for all core exceptions
     *      ControllerException     Inherits from CoreException
     *      ViewException           Inherits from CoreException
     *      ParserException         Inherits from CoreException
     *      RouteException          Inherits from CoreException
     *      SessionException        Inherits from CoreException
     *
     * ErrorException               Catchable errors will also throw ErrorException
     */
    $errorPage = new \app\Controller\Error();
    $errorPage->viewException($e);
}
