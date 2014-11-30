<?php

class AuthController extends Zend_Controller_Action {

    protected $flashMessenger;
    
    public function init() {
        
        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger",$this->flashMessenger);
        
    }

    public function indexAction() {

    }

    public function loginAction() {


        if ($this->getRequest()->isPost()) {

            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];


            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            //Inicia o adaptador Zend_Auth para banco de dados
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

            $authAdapter->setTableName('t_usuario')
                    ->setIdentityColumn('login')
                    ->setCredentialColumn('senha');
            /* ->setCredentialTreatment('SHA1(?)'); */

            //Define os dados para processar o login
            $authAdapter->setIdentity($usuario)
                    ->setCredential($senha);
            //Efetua o login
            $auth = Zend_Auth::getInstance();
            
            


            $result = $auth->authenticate($authAdapter);
            //Verifica se o login foi efetuado com sucesso
            if ($result->isValid()) {

                //Armazena os dados do usuário em sessão, apenas desconsiderando
                
                $info = $authAdapter->getResultRowObject(null, 'senha');
                $storage = $auth->getStorage();
                $storage->write($info);
                //Redireciona para o Controller protegido
                return $this->_helper->redirector->goToRoute(array('controller' => 'solicitacao'), null, true);
            } else {
                //Dados inválidos
                $this->flashMessenger->addMessage(array( 'danger' => "Login ou senha inválidos!"));
                return $this->_helper->redirector('index');
            }
        }
    }

    public function logoutAction() {

        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector('index');
    }

}
