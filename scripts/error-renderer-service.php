<?php

use Tuum\View\ErrorView;
use Tuum\Web\App;
use Tuum\Web\Http\Response;

/** @var App $app */

$view = new ErrorView($app->renderer(), $app->get(App::LOGGER));

/*
 * set up error templates.
 */

// default error template file name.
$view->default_error_file = 'errors/error';

// error template files for each error status code.
$view->error_files[Response::HTTP_FORBIDDEN] = 'errors/forbidden';
$view->error_files[Response::HTTP_NOT_FOUND] = 'errors/not-found';

$app->set( 'error-renderer-service', $view );
return $view;
