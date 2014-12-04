<?php

class ProdutoController extends Zend_Controller_Action {

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
        $categorias = $tCategoria->listarCategorias();

        $tProduto = new Produto();
        $listaDeProdutos = $tProduto->listarProdutos();

        $this->view->listaDeProdutos = $listaDeProdutos->toArray();
        $this->view->categorias = $categorias->toArray();
    }

    public function inserirAction() {

        $tCategoria = new Categoria();
        $categorias = $tCategoria->listarCategorias();

        $this->view->categorias = $categorias;

        if ($this->getRequest()->isPost()) {

            $categoriaid = $_POST['categoriaid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $quantidade = $_POST['quantidade'];
            $quantidademinima = $_POST['quantidademinima'];
            $validade = $_POST['validade'];

            $post = array('categoriaid' => $categoriaid, 'nome' => $nome, 'descricao' => $descricao,
                'quantidade' => $quantidade, 'quantidademinima' => $quantidademinima, 'validade' => $validade);

            try {
                $tProduto = new Produto();
                $novoProduto = $tProduto->inserirProduto($post);
                $this->flashMessenger->addMessage(array('success' => "O novo produto foi cadastrado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isso é constangedor! Por algum motivo seu produto não foi cadastrado. Tente novamente!"));
            };

            $this->_helper->redirector('buscarprodutoporcategoria');
        }
    }

    public function editarAction() {

        $tCategoria = new Categoria();
        $categorias = $tCategoria->listarCategorias();
        $this->view->categorias = $categorias;

        $id = $this->_getParam("id");
        $tProduto = new Produto();
        $produto = $tProduto->findProdutoById($id);

        $this->view->produto = $produto->current();

        if ($this->getRequest()->isPost()) {

            $id = $_POST['id'];

            $categoriaid = $_POST['categoriaid'];
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $validade = $_POST['validade'];

            $post = array('id' => $id, 'categoriaid' => $categoriaid, 'nome' => $nome, 'descricao' => $descricao,
                'validade' => $validade);


            try {

                $produtoEditado = $tProduto->editarProduto($post, $produto);
                $this->flashMessenger->addMessage(array('success' => "Produto editado com sucesso!"));
            } catch (Exception $e) {

                $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! O produto não foi editado. Tente novamente!"));
            };

            $this->_helper->redirector('buscarprodutoporcategoria');
        }
    }

    public function deletarAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam("id");

        $tProduto = new DbTable_Produto();
        $Produto = $tProduto->find($id);

        try {
            $Produto->current()->delete();
            $this->flashMessenger->addMessage(array('success' => "Produto deletado com sucesso!"));
        } catch (Exception $e) {
            $this->flashMessenger->addMessage(array('danger' => "Bem, isto é constrangedor! O produto não foi deletado. Tente novamente!"));
        }

        return $this->_helper->redirector('buscarprodutoporcategoria');
    }

    public function buscarprodutoporcategoriaAction() {

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $categoriaid = $this->_getParam("categoriaid");

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;

        if ($categoriaid) {
            
            $listaItensProibidos = array();

            if (isset($_POST['listaDeProdutos']) <> 'PERMISSAO') {

                $tProdutosolicitacao = new DbTable_Produtosolicitacao();
                $listaItensProibidos = $tProdutosolicitacao->verificarSeJahExisteItemEmSolicitacaoAtivaDoUsuario($usuarioId);
            }


            $tProdutoSolicitacao = new Produtosolicitacao;
            $listaDeItensPermitidos = $tProdutoSolicitacao->listarProdutosPermitidos($categoriaid, $listaItensProibidos);

            $this->view->listaDeProdutos = $listaDeItensPermitidos->toArray();
        }
    }

    public function listarprodutosindisponiveisAction() {


        $tProduto = new DbTable_Produto();
        $produtosIndisponiveis = $tProduto->listarIndisponiveis();

        $this->view->lisdaDeIndisponiveis = $produtosIndisponiveis;
    }

    public function listarprodutosquantidademinimaAction() {


        $tProduto = new DbTable_Produto();
        $produtosQuantidadeMinima = $tProduto->listarQuantidadeMinima();

        $this->view->lisdaDeQuantidadeMinima = $produtosQuantidadeMinima;
    }

}
