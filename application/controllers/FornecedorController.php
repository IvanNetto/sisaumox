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


            $post = array('id'=> $_POST['id'], 'razao_social' => $_POST['razao_social'], 'telefone1' => $_POST['telefone1'], 'telefone2' => $_POST['telefone2'],
                    'rua' => $_POST['rua'], 'bairro' => $_POST['bairro'], 'numero' => $_POST['numero'], 'complemento' => $_POST['complemento'], 
                    'cidade' => $_POST['cidade'], 'estado' => $_POST['estado'], 'contato' => $_POST['contato'], 'email' => $_POST['email']);
            
            try {
                $novoFornecedor = $perfil->inserirFornecedor($post);                
                $this->flashMessenger->addMessage(array( 'success' => "Um novo fornecedor foi cadastrado com sucesso!"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "Bem, isto é constrangedor. Um novo fornecedor não foi cadastrado. Tente novamente!"));

            };

            $this->_helper->redirector('listar');
        }
    }

    public function editarAction() {
        
        $id = $this->_getParam("id");
        $tFornecedor = new Fornecedor();
        $fornecedor = $tFornecedor->findFornecedorById($id);

        $this->view->fornecedor = $fornecedor->current();

        if ($this->getRequest()->isPost()) {

            
            $post = array('id'=> $_POST['id'], 'razao_social' => $_POST['razao_social'], 'telefone1' => $_POST['telefone1'], 'telefone2' => $_POST['telefone2'],
                    'rua' => $_POST['rua'], 'bairro' => $_POST['bairro'], 'numero' => $_POST['numero'], 'complemento' => $_POST['complemento'], 
                    'cidade' => $_POST['cidade'], 'estado' => $_POST['estado'], 'contato' => $_POST['contato'], 'email' => $_POST['email']);

            try {
               
                $perfilEditado = $tFornecedor->editarFornecedor($post, $perfil);
                $this->flashMessenger->addMessage(array('success' => "O fornecedor foi editado com sucesso!"));

            }catch (Exception $e){

                $this->flashMessenger->addMessage(array( 'danger' => "Bem, isto é constrangedor. O fornecedor não foi editado. Tente novamente!"));

            };

             $this->_helper->redirector('listar');
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
            $this->flashMessenger->addMessage(array( 'success' => "Fornecedor excluido com sucesso!"));

        } catch (Exception $e) {

            echo $e->getMessage();
            $this->flashMessenger->addMessage(array( 'success' => "Bem, isto é constrangedor. O fornecedor não foi excluido. Tente novamente!"));
        }

        return $this->_helper->redirector('listar');
    }

}
