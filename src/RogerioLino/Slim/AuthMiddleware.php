<?php
namespace RogerioLino\Slim;

/**
 * AuthMiddleware
 *
 * @author Rogerio Lino <rogeriolino@gmail.com>
 */
class AuthMiddleware extends \Slim\Middleware {
    
    const SESS_USER = 'auth.user';
    
    private $loginPage = '/login';
    private $logoutPage = '/logout';
    private $homePage = '/index';
    private $viewVar = 'username';
    
    public function __construct() {
        @session_start();
    }

    public function register($username) {
        $this->username($username);
        $this->app->redirect($this->app->request()->getRootUri() . $this->homePage);
    }
    
    public function destroy() {
        unset($_SESSION[self::SESS_USER]);
    }
    
    public function username($username = null) {
        if (!$username) {
            return $_SESSION[self::SESS_USER];
        }
        $_SESSION[self::SESS_USER] = $username;
    }
    
    public function isLoginPage() {
        return $this->app->request()->getResourceUri() === $this->loginPage;
    }
    
    public function isLogoutPage() {
        return $this->app->request()->getResourceUri() === $this->logoutPage;
    }
    
    public function isHomePage() {
        return $this->app->request()->getResourceUri() === $this->homePage;
    }
    
    public function isLogged() {
        return isset($_SESSION[self::SESS_USER]) && !empty($_SESSION[self::SESS_USER]);
    }
    
    public function hasAccess() {
        return true;
    }
    
    public function call() {
        if (!$this->isLoginPage() && !$this->isLogoutPage() && (!$this->isLogged() || !$this->hasAccess())) {
            $this->app->redirect($this->app->request()->getRootUri() . $this->loginPage);
        } else {
            if ($this->isLogged()) {
                $this->app->view()->set($this->viewVar, $this->username());
            }
            $this->next->call();
        }
    }
    
    public function getLoginPage() {
        return $this->loginPage;
    }

    public function getLogoutPage() {
        return $this->logoutPage;
    }

    public function getHomePage() {
        return $this->homePage;
    }

    public function setLoginPage($loginPage) {
        $this->loginPage = $loginPage;
    }

    public function setLogoutPage($logoutPage) {
        $this->logoutPage = $logoutPage;
    }

    public function setHomePage($homePage) {
        $this->homePage = $homePage;
    }

}
