<?php
namespace RogerioLino\Rest;

use Doctrine\ORM\EntityManager;

/**
 * EntityApi
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
abstract class EntityApi {
    
    /**
     * @var EntityManager
     */
    protected $em;
    private $entityName;
    
    public function __construct(EntityManager $em, $entityName) {
        $this->em = $em;
        $this->entityName = $entityName;
    }
    
    public abstract function hydrate($entity, $data);
    
    protected function fields($alias = "e") {
        return "$alias.id";
    }
    
    protected function joins($alias = "e") {
        return "";
    }
    
    protected function where($alias = "e") {
        return " 1 = 1 ";
    }
    
    protected function orderBy($alias = "e") {
        return "$alias.id";
    }
    
    protected function dql($where = null, $orderBy = null) {
        $where = (!$where ? "" : "$where AND ") . $this->where();
        if (!$orderBy) {
            $orderBy = $this->orderBy();
        }
        return "SELECT {$this->fields()} FROM {$this->entityName} e {$this->joins()} WHERE {$where} ORDER BY {$orderBy}";
    }
    
    public function get($id) {
        try {
            return $this->em->createQuery($this->dql("e.id = :id"))
                    ->setParameter('id', (int) $id)
                    ->getSingleResult();
        } catch (\Exception $e) {
            return array("error" => $e->getMessage());
        }
    }
    
    public function all($maxResult, $offset = 0) {
        $rs = $this->em->createQuery($this->dql())
                ->setFirstResult($offset)
                ->setMaxResults($maxResult)
                ->getResult();
        return $rs;
    }
    
    public function put($id, $data) {
        try {
            $entity = $this->em->find($this->entityName, (int) $id);
            $this->hydrate($entity, $data);
            $this->em->merge($entity);
            $this->em->flush();
            return $entity->getId();
        } catch (\Exception $e) {
            return array("error" => $e->getMessage());
        }
    }
    
    public function post($data) {
        try {
            $entity = new $this->entityName;
            $this->hydrate($entity, $data);
            $this->em->persist($entity);
            $this->em->flush();
            return $entity->getId();
        } catch (\Exception $e) {
            return array("error" => $e->getMessage());
        }
    }
    
    public function json($rs) {
        return json_encode($rs);
    }
    
}
