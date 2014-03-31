<?php
namespace RogerioLino\Form;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * BasicValidator
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class Paginator {
    
    /**
     * @var \Doctrine\ORM\Query
     */
    protected $query;
    /**
     * @var integer
     */
    protected $maxResults = 20;
    /**
     * @var integer
     */
    protected $range = 10;
    /**
     * @var integer
     */
    protected $totalPages = 0;
    /**
     * @var integer
     */
    protected $totalItems = 0;
    /**
     * @var integer
     */
    protected $startPage = 0;
    /**
     * @var integer
     */
    protected $currentPage = 0;
    /**
     * @var integer
     */
    protected $endPage = 0;
    /**
     * @var integer
     */
    protected $paginator;
    
    /**
     * @param \Doctrine\ORM\Query $query
     * @param integer $maxResults
     * @return \RogerioLino\Form\Paginator
     */
    public static function create(Query $query, $maxResults) {
        $instance = new Paginator();
        return $instance->query($query)->maxResults((int) $maxResults);
    }

    /**
     * @param \Doctrine\ORM\Query $query
     * @return \RogerioLino\Form\Paginator
     */
    public function query(Query $query) {
        $this->query = $query;
        return $this;
    }
    
    /**
     * @param integer $maxResults
     * @return \RogerioLino\Form\Paginator
     */
    public function maxResults($maxResults) {
        if ($maxResults > 0) {
            $this->maxResults = $maxResults;
        }
        return $this;
    }
    
    /**
     * @param integer $range
     * @return \RogerioLino\Form\Paginator
     */
    public function range($range) {
        $this->range = $range;
        return $this;
    }

    /**
     * @param integer $currentPage [optional]
     * @return \RogerioLino\Form\Paginator
     */
    public function paginate($currentPage = 0) {
        $this->query
                ->setFirstResult($currentPage * $this->maxResults)
                ->setMaxResults($this->maxResults)
        ;
        $this->currentPage = $currentPage;
        $this->paginator = new DoctrinePaginator($this->query, true);
        $this->totalItems = count($this->paginator);
        $this->totalPages = floor($this->totalItems / $this->maxResults);
        
        $min = floor($currentPage - $this->range / 2);
        $max = floor($currentPage + $this->range / 2);
        if ($min < 0) {
            $max += abs($min);
        }
        if ($max > $this->totalPages) {
            $min -= $max - $this->totalPages;
        }
        $this->startPage = max(array(0, $min));
        $this->endPage = min(array($this->totalPages, $max));
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'items' => $this->paginator,
            'totalItems' => $this->totalItems,
            'page' => $this->currentPage,
            'maxResults' => $this->maxResults,
            'totalPages' => $this->totalPages,
            'startPage' => $this->startPage,
            'endPage' => $this->endPage
        );
    }
    
    
}
