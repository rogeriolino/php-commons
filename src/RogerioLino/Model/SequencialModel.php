<?php
namespace RogerioLino\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @author Rogério Lino <rogeriolino@gmail.com>
 * 
 * @ORM\MappedSuperclass
 */
abstract class SequencialModel extends Model {
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
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
