<?php

class PessoaController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('listar');
    }

    public function listarAction() {


        $tPessoa = new DbTable_Pessoa();
        $listaDePessoas = $tPessoa->fetchAll();

        $this->view->listaDePessoas = $listaDePessoas->toArray();
    }

    public function inserirAction() {

        $tFuncao = new Funcao();
        $funcoes = $tFuncao->listarFuncoes();

        $this->view->funcoes = $funcoes;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            $matricula = $_POST['matricula'];
            $nome = $_POST['nome'];
            $funcaoid = $_POST['funcaoid'];
            $telefone1 = $_POST['telefone1'];
            $telefone2 = $_POST['telefone2'];

            $post = array('matricula' => $matricula, 'nome' => $nome, 'funcaoid' => $funcaoid, 'telefone1' => $telefone1, 'telefone2' => $telefone2);

            try {
                $tPessoa = new Pessoa();
                $novaPessoa = $tPessoa->inserirPessoa($post);
                $this->flashMessenger->addMessage(array('success' => "Um novo funcionário foi cadastrado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isso é constangedor! Por algum motivo seu funcionário não foi cadastrado. Tente novamente!"));
            };
            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {

        $tFuncao = new Funcao();
        $funcoes = $tFuncao->listarFuncoes();
        $this->view->funcoes = $funcoes;

        $id = $this->_getParam("id");
        $tPessoa = new Pessoa();
        $pessoa = $tPessoa->findPessoaById($id);

        $this->view->pessoa = $pessoa->current();


        if ($this->getRequest()->isPost()) {

            $formData = $this->getRequest()->getPost();

            $matricula = $_POST['matricula'];
            $nome = $_POST['nome'];
            $funcaoid = $_POST['funcaoid'];
            $telefone1 = $_POST['telefone1'];
            $telefone2 = $_POST['telefone2'];

            $post = array('matricula' => $matricula, 'nome' => $nome, 'funcaoid' => $funcaoid, 'telefone1' => $telefone1, 'telefone2' => $telefone2);

            try {
                
                $pessoaEditada = $tPessoa->editarPessoa($post, $pessoa);
                $this->flashMessenger->addMessage(array('success' => "Funcionario cadastrado com sucesso"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto e constrangedor. Algum erro ocorreu!"));
            };

            $this->forward('index', 'pessoa', $post);
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tPessoa = new DbTable_Pessoa();
        $pessoa = $tPessoa->find($id);

        try {
            $pessoa->current()->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->_helper->redirector('listar');
    }

}
