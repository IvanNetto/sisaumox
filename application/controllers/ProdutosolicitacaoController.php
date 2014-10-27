<?php

class ProdutosolicitacaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {

        return $this->_helper->redirector('');
    }

    public function buscarprodutosporcategoriaAction() {

        $categoriaid = $this->_getParam("categoriaid");
        $solicitacaoid = $this->_getParam("solicitacaoid");
        

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;

        if ($categoriaid) {

            $tProduto = new Produto();
            $listaDeProdutos = $tProduto->findProdutoByCategoriaid($categoriaid);

            $this->view->listaDeProdutos = $listaDeProdutos->toArray();
        }

        $this->view->solicitacaoid = $solicitacaoid;
    }

    public function inserirAction() {

        $produtosescolhidos = ($_POST['checkbox']);
        $solicitacaoid = ($_POST['solicitacaoid']);
        
        
        $categoriaid = ($_POST['categoriaid']);
        
        $produtoid = $this->_getParam("id");
       


        try {

            $tProdutosolicitacao = new Produtosolicitacao();
            $novoprodutosolicitacao = $tProdutosolicitacao->inserirProdutoSolicitacao($solicitacaoid, $produtosescolhidos);
        } catch (Exception $e) {

            $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        $params = array('categoriaid' => $categoriaid, 'solicitacaoid' => $solicitacaoid);

        
//esta voltando para 
        return $this->forward('buscarprodutosporcategoria', 'produtosolicitacao', null, $params);
    }

    public function carrinhodesolicitacoesAction() {

        $solicitacaoid = $this->_getParam('solicitacaoid');


        $ProdutoSolicitacao = new Produtosolicitacao();
        $conteudoDoCarrinho = $ProdutoSolicitacao->exibirCarrinhoDeSolicitacoes($solicitacaoid);

        $this->view->conteudoDoCarrinho = $conteudoDoCarrinho;
        $this->view->solicitacaoid = $solicitacaoid;
    }

    public function atualizarprodutosesolicitacaoAction() {

        $produtos = $_POST['produto'];
        $operacao = '-';
        $quantidade = $_POST['quantidade'];

        if ($_POST['solicitacaoid']){

        $solicitacaoid = $_POST['solicitacaoid'];

        }else{
            $solicitacaoid = $this->_getParam('solicitacaoid');
        }


        if ($produtos) {


            $tProduto = new Produto();
            $atualizaProduto = $tProduto->atualizarEstoque($produtos, $operacao, $quantidade);
        }

        $tsolicitacao = new Solicitacao();
        $statusatual = $tsolicitacao->mostrarStatusAtual($solicitacaoid)->current()->status;
        $atualizaSolicitacao = $tsolicitacao->atualizarStatus($solicitacaoid, $statusatual);
        
       //como eu mando pra solicitacao/listar?
        return $this->_helper->redirector->gotoSimple('listar','solicitacao');
        
        
    }
    
    public function resumodesolicitacaoAction(){
        
         $solicitacaoid = $this->_getParam('solicitacaoid');
        
         $ProdutoSolicitacao = new Produtosolicitacao();
         $resumoDeSolicitacao = $ProdutoSolicitacao->resumoDeSolicitacao($solicitacaoid);
         
         $this->view->resumoDeSolicitacao = $resumoDeSolicitacao;
        
    }
    
    public function cancelarcarrinhodecomprasAction(){
        
        
        $solicitacaoid = $this->_getParam('solicitacaoid');
        
        $ProdutoSolicitacao = new Produtosolicitacao();
        $carrinhocancelado = $ProdutoSolicitacao->cancelarCarrinhoDeCompras($solicitacaoid);
        
        return $this->_helper->redirector('solicitacao/listar');
        
        
    }

}
