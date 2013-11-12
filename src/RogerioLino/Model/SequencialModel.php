<?php
namespace RogerioLino\Model;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;

/**
 * 
 * @author Rogério Lino <rogeriolino@gmail.com>
 * 
 * @MappedSuperclass
 */
abstract class SequencialModel extends Model {
    
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="id", type="integer", nullable=false)
     * @var integer
     */
    private $id;
    
    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
}
