<?php
namespace Tuum\View;

use Exception;
use Tuum\Web\App;
use Tuum\Web\Http\Response;

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
     * @param ViewEngineInterface $app
     */    
    public function __construct($app)
    {
        $this->engine = $app;
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
        $code = $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
        $content = $this->render($code, $data);
        echo $content;
        exit;
    }

    /**
     * @param int   $code
     * @param array $data
     * @return string
     */    
    public function render($code, $data=[])
    {
        $error = isset( $this->error_files[$code] ) ? $this->error_files[$code] : $this->default_error_file;
        if( !$error ) {
            return '';
        }
        $content = $this->engine->render($error, $data);
        return $content;
    }
}