<?php
namespace RogerioLino\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use RogerioLino\Form\EntityValidator;
use RogerioLino\Util\Arrays;
use Slim\Slim;

/**
 * CrudController
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
abstract class CrudController extends SlimController {
        
    protected $name;
    protected $entityName;
    protected $maxResults = 20;
    protected $readonly = false;
    
    public function __construct(Slim $app, $entityName, $title = "", $subtitle = "") {
        parent::__construct($app);
        $this->entityName = $entityName;
        // resolving controller name (class prefix)
        $this->name = strtolower(str_replace('Controller', '', @end(@explode('\\', get_class($this)))));
        
        $this->app()->view()->set('title', $title);
        $this->app()->view()->set('subtitle', $subtitle);
    }
    
    /**
     * @return EntityManager
     */
    public final function em() {
        return $this->app()->em;
    }
    
    public function index() {
        $this->postIndex();
        $page = (int) Arrays::value($_GET, 'page', 0);
        $search = Arrays::value($_GET, 's', '');
        $query = $this->searchQuery("%$search%", $this->maxResults, $page * $this->maxResults);
        $paginator = new Paginator($query, true);
        $total = count($paginator);

        $this->app()->view()->set('controllerName', $this->name);
        
        $this->app()->view()->set('search', $search);
        $this->app()->view()->set('items', $paginator);
        $this->app()->view()->set('total', $total);
        $this->app()->view()->set('page', $page);
        $this->app()->view()->set('maxResults', $this->maxResults);
        $this->app()->view()->set('pages', ceil($total / $this->maxResults));
    }
    
    protected function searchQuery($searchValue, $maxResults, $first = 0) {
        $dql = "SELECT e FROM {$this->entityName} e";
        $query = $this->em()->createQuery($dql)
                    ->setFirstResult($first)
                    ->setMaxResults($maxResults);
        return $query;
    }
    
    public function edit($id = 0) {
        $model = null;
        $id = (int) $id;
        if ($id > 0) {
            $model = $this->em()->find($this->entityName, $id);
        }
        if (!$model) {
            $model = new $this->entityName;
        }
        
        $this->preEdit($model);
        
        // form submit
        if ($this->app()->request()->isPost()) {
            if (!$this->readonly) {
                $form = new EntityValidator($model);
                $form->hydrate($this->app()->request()->post('data'));
                try {
                    if ($form->isValid()) {
                        $this->preSave($model);
                        $redirectUrl = $_SERVER['HTTP_REFERER'];
                        if ($model->getId() > 0) {
                            $this->em()->merge($model);
                            $message = 'Registro atualizado com sucesso';
                        } else {
                            $this->em()->persist($model);
                            $message = 'Registro adicionado com sucesso';
                            if ($redirectUrl[strlen($redirectUrl) - 1] !== '/') {
                                $redirectUrl .= '/';
                            }
                        }
                        $this->em()->flush();
                        $this->postSave($model);
                        $this->app()->flash('success', $message);
                        if ($redirectUrl[strlen($redirectUrl) - 1] === '/') {
                            $redirectUrl .= $model->getId();
                        }
                        $this->app()->redirect($redirectUrl);
                    }
                } catch (Exception $e) {
//                    echo $e->getTraceAsString() . '<br><br>';
//                    echo $e->getFile() . ':' . $e->getLine(); exit();
                    $this->app()->view()->set('error', $e->getMessage());
                }
            }
        }
        $this->postEdit($model);
        
        $this->app()->view()->set('controllerName', $this->name);
        $this->app()->view()->set('model', $model);
        $this->app()->view()->set('readonly', $this->readonly);
    }
    
    protected function postIndex() {}
    protected function preEdit($model) {}
    protected function postEdit($model) {}
    protected function preSave($model) {}
    protected function postSave($model) {}

    
}
