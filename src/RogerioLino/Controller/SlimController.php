<?php
namespace RogerioLino\Controller;

use Slim\Slim;

/**
 * CrudController
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
abstract class SlimController {
        
    private $app;
    
    public function __construct(Slim $app) {
        $this->app = $app;
    }
    
    /**
     * @return Slim
     */
    public function app() {
        return $this->app;
    }

    
}
