<?php
namespace RogerioLino\Form;

/**
 * BasicValidator
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class BasicValidator implements Validator {
    
    private $requireds = array();
    private $data = array();
    
    /**
     * Set requireds values
     * @param array $requireds
     * @return \RogerioLino\Form\BasicValidator
     */
    public function requireds(array $requireds) {
        $this->requireds = $requireds;
        return $this;
    }
    
    /**
     * Set the data to be validated
     * @param array $data
     * @return \RogerioLino\Form\BasicValidator
     */
    public function data(array $data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @throws Exception
     * @return boolean
     */
    public function isValid() {
        foreach ($this->requireds as $req) {
            if (!isset($this->data[$req]) || empty($this->data[$req])) {
                throw new \Exception(sprintf('Campo obrigatório: %s', $req));
            }
        }
        return true;
    }
    
}
