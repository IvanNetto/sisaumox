<?php

class SetorController extends Zend_Controller_Action {

    protected $flashMessenger;


    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger",$this->flashMessenger);

    }

    public function indexAction() {

        return $this->_helper->redirector('listar');

    }

    public function listarAction() {


        $tSetor = new Setor();
        $listaDeSetores = $tSetor->listarSetores();

        $this->view->listaDeSetores = $listaDeSetores->toArray();
    }

    public function inserirAction() {

        $setor = new Setor();
        
        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);
            
            try {
                $novoSetor = $setor->inserirSetor($post);                
                $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "ops!"));

            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {
        
        $id = $this->_getParam("id");
        $tSetor = new Setor();
        $setor = $tSetor->findSetorById($id);

        $this->view->setor = $setor->current();

        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);

            try {
               
                $setorEditado = $tSetor->editarSetor($post, $setor);
                $this->flashMessenger->addMessage(array('success' => "tudo ok"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "ops!"));

            };

             $this->forward('index','setor', null, $post);
        }
    }
    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tSetor = new DbTable_Setor();
        $setor = $tSetor->find($id);

        try {

            $setor->current()->delete();
            $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));

        } catch (Exception $e) {

            echo $e->getMessage();
            $this->flashMessenger->addMessage(array( 'success' => "tudo ok"));
        }

        return $this->_helper->redirector('listar');
    }

}
