<?php
namespace RogerioLino\Form;

/**
 * Validator
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
interface Validator {
    
    /**
     * @throws Exception
     * @return boolean
     */
    public function isValid();
    
}
