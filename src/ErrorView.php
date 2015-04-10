<?php
namespace Tuum\View;

use Exception;
use Psr\Log\LoggerInterface;
use Tuum\Web\Psr7\Respond;
use Tuum\Web\Psr7\Response;

class ErrorView
{
    /**
     * @var ViewEngineInterface
     */
    protected $engine;

    /**
     * default error file name.
     *
     * @var string
     */
    public $default_error_file = 'errors/error';

    /**
     * error file names for each status code.
     *
     * @var array
     */
    public $error_files = [];

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @param ViewEngineInterface $engine
     * @param bool                $debug
     */
    public function __construct($engine, $debug = false)
    {
        $this->engine = $engine;
        $this->debug  = $debug;
    }

    /**
     * error handler for production environment.
     * returns a response with error page.
     *
     * @param Exception $e
     * @return Response
     */
    public function __invoke($e)
    {
        $data['message'] = $e->getMessage();
        $code            = $e->getCode() ?: Respond::INTERNAL_ERROR;
        if ($this->logger) {
            $this->logger->critical('ErrorView: caught ' . get_class($e) . "({$code}), " . $e->getMessage(),
                $e->getTrace());
        }
        if ($this->debug) {
            $data['trace'] = $e->getTrace();
        }
        $content = $this->render($code, $data);
        echo $content;
        exit;
    }

    /**
     * @param int   $code
     * @param array $data
     * @return string
     */
    public function render($code, $data = [])
    {
        $error = isset($this->error_files[$code]) ? $this->error_files[$code] : $this->default_error_file;
        if (!$error) {
            return '';
        }
        $content = $this->engine->render($error, $data);
        return $content;
    }

    /**
     * @param null|LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}