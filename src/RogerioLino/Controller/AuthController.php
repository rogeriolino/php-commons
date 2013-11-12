<?php
namespace RogerioLino\Controller;

use Doctrine\ORM\EntityManager;
use Exception;
use RogerioLino\Security\Hasher;

/**
 * AuthController
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class AuthController {
    
    /**
     * @var EntityManager
     */
    private $em;
    private $entityName;
    private $usernameField;
    private $passwordField;
    
    public function __construct(EntityManager $em, $entityName, $usernameField, $passwordField) {
        $this->em = $em;
        $this->entityName = $entityName;
        $this->usernameField = $usernameField;
        $this->passwordField = $passwordField;
    }
    
    public function hash($password) {
        $hasher = new Hasher(PASS_SALT);
        return $hasher->password($password);
    }
    
    public function auth($username, $password) {
        try {
            $dql = "SELECT e.{$this->usernameField}, e.{$this->passwordField} FROM {$this->entityName} e WHERE e.{$this->usernameField} = :username";
            $rs = $this->em->createQuery($dql)
                    ->setParameter('username', $username)
                    ->getSingleResult();
            if ($rs[$this->passwordField] !== $this->hash($password)) {
                throw new Exception();
            }
        } catch (Exception $e) {
            throw new Exception('Usuário ou senha inválido');
        }
    }
    
    public function get($username) {
        return $this->em->createQueryBuilder()
                ->select('e')
                ->from($this->entityName, 'e')
                ->where("e.{$this->usernameField} = :username")
                ->setParameter("username", $username)
                ->getQuery()
                ->getSingleResult();
    }
    
}
