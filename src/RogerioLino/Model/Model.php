<?php
namespace RogerioLino\Model;

/**
 * 
 * @author Rogério Lino <rogeriolino@gmail.com>
 * 
 */
class Model {
    
    public function toString() {
        return get_class($this);
    }
    
    public function __toString() {
        $this->toString();
    }
    
}
