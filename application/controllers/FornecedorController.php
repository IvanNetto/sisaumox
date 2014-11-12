<?php

class FornecedorController extends Zend_Controller_Action {

    protected $flashMessenger;


    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger",$this->flashMessenger);

    }

    public function indexAction() {

        return $this->_helper->redirector('listar');

    }

    public function listarAction() {

        $tFornecedor = new Fornecedor();
        $listaDeFornecedores = $tFornecedor->listarFornecedores();

        $this->view->listaDeFornecedores = $listaDeFornecedores->toArray();
        
    }

    public function inserirAction() {

        $perfil = new Fornecedor();
        
        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);
            
            try {
                $novoFornecedor = $perfil->inserirFornecedor($post);                
                $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "ops!"));

            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {
        
        $id = $this->_getParam("id");
        $tFornecedor = new Fornecedor();
        $perfil = $tFornecedor->findFornecedorById($id);

        $this->view->perfil = $perfil->current();

        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);

            try {
               
                $perfilEditado = $tFornecedor->editarFornecedor($post, $perfil);
                $this->flashMessenger->addMessage(array('success' => "tudo ok"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "ops!"));

            };

             $this->forward('index','perfil', null, $post);
        }
    }
    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tFornecedor = new DbTable_Fornecedor();
        $perfil = $tFornecedor->find($id);

        try {

            $perfil->current()->delete();
            $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));

        } catch (Exception $e) {

            echo $e->getMessage();
            $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));
        }

        return $this->_helper->redirector('listar');
    }

}
