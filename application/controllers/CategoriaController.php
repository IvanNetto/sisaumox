<?php

class CategoriaController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {

        $tCategoria = new Categoria();
        $listaDeCategorias = $tCategoria->listarCategorias();

        $this->view->listaDeCategorias = $listaDeCategorias->toArray();
    }

    public function inserirAction() {

        $tCategoria = new Categoria();

        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);

            try {
                $novaCategoria = $tCategoria->inserirCategoria($post);
                $this->flashMessenger->addMessage(array('success' => "Categoria cadastrada com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! A categoria não foi cadastrada. Tente novamente!"));
            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {

        $id = $this->_getParam("id");
        $tCategoria = new Categoria();
        $categoria = $tCategoria->findCategoriaById($id);
        
        $this->view->categoria = $categoria->current();

        if ($this->getRequest()->isPost()) {

            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];

            $post = array('nome' => $nome, 'descricao' => $descricao);

            try {

                $categoriaEditado = $tCategoria->editarCategoria($post, $categoria);
                $this->flashMessenger->addMessage(array('success' => "A categoria foi atualizada com sucesso"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! Mas a categoria não foi editada. Tente novamente!"));
            };

            $this->forward('index', 'categoria', null, $post);
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tCategoria = new DbTable_Categoria();
        $Categoria = $tCategoria->find($id);

        try {
            $Categoria->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }


    public function selecionarcategoriaAction()
    {

        $idSolicitacao = $this->_getParam("id");
        

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;
        $this->view->idsolicitacao = $idSolicitacao;


    }

}
