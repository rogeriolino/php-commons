<?php
namespace RogerioLino\Security;

/**
 * Hasher
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class Hasher {
    
    private $salt;
    private $algorithm;
    
    public function __construct($salt = 'ChangeMePlease!', $algorithm = 'sha1') {
        $this->salt = $salt;
        $this->algorithm = $algorithm;
    }
    
    public function password($plainPassword) {
        return hash($this->algorithm, $plainPassword . $this->salt);
    }
    
}
