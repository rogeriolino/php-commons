<?php
namespace RogerioLino\Form;

use RogerioLino\Model\Model;
use Doctrine\ORM\Mapping as ORM;

/**
 * EntityValidator
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class EntityValidator implements Validator {
    
    /**
     * @var Model
     */
    private $model;
    
    public function __construct(Model $model) {
        $this->model = $model;
    }
    
    public function hydrate(array $data) {
        if ($this->model) {
            foreach ($data as $k => $v) {
                $class = get_class($this->model);
                do {
                    try {
                        $prop = new \ReflectionProperty($class, $k);
                        $prop->setAccessible(true);
                        $prop->setValue($this->model, $v);
                        break;
                    } catch (\Exception $e) {
                        $class = get_parent_class($class);
                    }
                } while ($class);
            }
        }
    }

    /**
     * @throws Exception
     * @return boolean
     */
    public function isValid() {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $class = get_class($this->model);
        $id = null;
        do {
            $ref = new \ReflectionClass($class);
            $props = $ref->getProperties();
            foreach ($props as $prop) {
                $annotations = $reader->getPropertyAnnotations($prop);
                foreach ($annotations AS $annot) {
                    $value = $this->getValue($prop, $this->model);
                    if ($annot instanceof ORM\Id) {
                        $id = $prop;
                    }
                    else if ($prop !== $id) {
                        if ($annot instanceof ORM\Column) {
                            if ($annot->nullable === false && empty($value)) {
                                throw new \Exception(sprintf('Campo obrigatório: %s', $prop->getName()));
                            }
                            switch ($annot->type) {
                            case 'string':
                                if ($annot->length && strlen($value) > $annot->length) {
                                    throw new \Exception(sprintf('Tamanho máximo do campo excedido: %s', $prop->getName()));
                                }
                                break;
                            case 'integer':
                                $int = (int) $value;
                                if (strcmp($int, $value) !== 0) {
                                    throw new \Exception(sprintf('O valor do campo %s não é um inteiro válido: %s', $prop->getName(), $value));
                                }
                                break;
                            }
                        }
                        else if ($annot instanceof ORM\JoinColumn) {
                            if ($annot->nullable === false && empty($value)) {
                                throw new \Exception(sprintf('Campo obrigatório: %s', $prop->getName()));
                            }
                        }
                    }
                }
            }
            $class = get_parent_class($class);
        } while ($class);
        return true;
    }
    
    private function getValue(\ReflectionProperty $prop, $obj) {
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
    
}
