<?php

use Tuum\View\ErrorView;
use Tuum\Web\App;
use Tuum\Web\Psr7\Respond;
use Tuum\Web\Web;
use Tuum\Web\Psr7\Response;

/** @var Web $app */

$view = new ErrorView($app->get(App::RENDER_ENGINE), $app->get(App::DEBUG));
$view->setLogger($app->get(App::LOGGER));
/*
 * set up error templates.
 */

// default error template file name.
$view->default_error_file = 'errors/error';

// error template files for each error status code.
$view->error_files[Respond::ACCESS_DENIED] = 'errors/forbidden';
$view->error_files[Respond::FILE_NOT_FOUND] = 'errors/not-found';

$app->set( 'error-renderer-service', $view );
return $view;
