<?php

class FuncaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {

        $tSetor = new Setor();
        $setores = $tSetor->listarSetores();

        $tFuncao = new Funcao();
        $listaDeFuncoes = $tFuncao->listarFuncoes();

        $this->view->setores = $setores->toArray();
        $this->view->listaDeFuncoes = $listaDeFuncoes->toArray();
    }

    public function inserirAction() {

        $tSetor = new Setor();
        $setores = $tSetor->listarSetores();

        $this->view->setores = $setores;

        if ($this->getRequest()->isPost()) {
            
            $setorid = $_POST['setorid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('setorid' => $setorid, 'nome' => $nome, 'descricao' => $descricao);

            try {
                $tFuncao = new Funcao();
                $novaFuncao = $tFuncao->inserirFuncao($post);
                $this->flashMessenger->addMessage(array('success' => "O novo funcao foi cadastrado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isso é constangedor! Por algum motivo seu funcao não foi cadastrado. Tente novamente!"));
            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {

         $tSetor = new Setor();
        $setores = $tSetor->listarSetores();

        $this->view->setores = $setores;

        $id = $this->_getParam("id");
        $tFuncao = new Funcao();
        $funcao = $tFuncao->findFuncaoById($id);

        $this->view->funcao = $funcao->current();

        if ($this->getRequest()->isPost()) {

             $setorid = $_POST['setorid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('setorid' => $setorid, 'nome' => $nome, 'descricao' => $descricao);


            try {

                $funcaoEditada = $tFuncao->editarFuncao($post, $funcao);
                $this->flashMessenger->addMessage(array('success' => "Função editada com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! Por algum motivo uma função não pode ser cadastrada. Tente novamente!"));
            };

            $this->forward('index', 'funcao', $post);
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tFuncao = new DbTable_Funcao();
        $Funcao = $tFuncao->find($id);

        try {
            $Funcao->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }

}
