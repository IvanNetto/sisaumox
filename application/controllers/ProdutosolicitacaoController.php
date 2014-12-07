<?php

class ProdutosolicitacaoController extends Zend_Controller_Action {

    protected $flashMessenger;

    public function init() {

        $this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
        Zend_Layout::getMvcInstance()->assign("flashMessenger", $this->flashMessenger);
    }

    public function indexAction() {
        
    }

    public function buscarprodutosporcategoriaAction() {

        $categoriaid = $this->_getParam("categoriaid");
        $solicitacaoid = $this->_getParam("solicitacaoid");

        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;

        $tCategoria = new DbTable_Categoria();
        $listadecategorias = $tCategoria->fetchAll();

        $this->view->listadecategorias = $listadecategorias;

        if ($categoriaid) {

            $tProdutosolicitacao = new DbTable_Produtosolicitacao();
            $listaItensProibidos = $tProdutosolicitacao->verificarSeJahExisteItemEmSolicitacaoAtivaDoUsuario($usuarioId);

            $tProdutoSolicitacao = new Produtosolicitacao;
            $listaDeItensPermitidos = $tProdutoSolicitacao->listarProdutosPermitidos($categoriaid, $listaItensProibidos);

            $this->view->listaDeProdutos = $listaDeItensPermitidos->toArray();
        }

        $this->view->solicitacaoid = $solicitacaoid;
    }

    public function inserirAction() {

        $produtosescolhidos = ($_POST['checkbox']);

        $solicitacaoid = ($_POST['solicitacaoid']);
        $categoriaid = ($_POST['categoriaid']);

        try {

            $tProdutosolicitacao = new Produtosolicitacao();
            $tProdutosolicitacao->inserirProdutoSolicitacao($solicitacaoid, $produtosescolhidos);

            $deucerto = true;
        } catch (Exception $e) {
            $deucerto = false;
        };

        $params = array('categoriaid' => $categoriaid, 'solicitacaoid' => $solicitacaoid, 'deucerto' => $deucerto);
        $this->forward('buscarprodutosporcategoria', 'produtosolicitacao', null, $params);
    }

    public function deletaritemdocarrinhoAction() {

        $solicitacaoid = $this->_getParam('solicitacaoid');
        $produtoid = $this->_getParam('produtoid');

        $tProdutosolicitacao = new Produtosolicitacao();
        $produtosolicitacao = $tProdutosolicitacao->findByProdutoESolicitacao($produtoid, $solicitacaoid);

        try {

            $produtosolicitacao->current()->delete();

            $deucerto = true;
        } catch (Exception $e) {
            $deucerto = false;
            $this->flashMessenger->addMessage(array('success' => "Bem, isto é constrangedor! Algo aconteceu com seu item. Recarregue a página (pressione F5 do seu teclado) e tente novamente"));
        }

        return $this->forward('carrinhodesolicitacoes', 'produtosolicitacao', null, ['solicitacaoid' => $solicitacaoid, 'deucerto' => $deucerto]);
    }

    public function carrinhodesolicitacoesAction() {


        if ($this->_getParam('deucerto')) {
            $this->flashMessenger->addMessage(array('success' => "Item removido do carrinho com sucesso"));
        }

        $solicitacaoid = $this->_getParam('solicitacaoid');

        $ProdutoSolicitacao = new Produtosolicitacao();
        $conteudoDoCarrinho = $ProdutoSolicitacao->exibirCarrinhoDeSolicitacoes($solicitacaoid);

        $this->view->conteudoDoCarrinho = $conteudoDoCarrinho;
        $this->view->solicitacaoid = $solicitacaoid;
    }

    /*
     * Atualiza primeiro o estoque - Depois o status da solicitação - Depois armazena a quantidade da solicitação
     */

    public function atualizarprodutosesolicitacaoAction() {

        $status = $this->_getParam('status');
        
        $solicitacaoid = $this->_getParam('solicitacaoid');
        $produtosolicitacaoId = $this->_getParam('produtosolicitacaoid');
        
        if ($status == 'reprovada') {

            $tProdutoSolicitacao = new DbTable_Produtosolicitacao;
            $produtoSolicitacao = $tProdutoSolicitacao->find($produtosolicitacaoId)->current();

            $tProdutoSolicitacao = new Produtosolicitacao;
            $produtos = $tProdutoSolicitacao->findBySolicitacao($solicitacaoid);
            
            $tProduto = new Produto;
            $tProduto->atualizarEstoqueComProdutosRejeitados($produtos, $produtoSolicitacao);

            return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');
        }

        $produtos = $_POST['produto'];
        $operacao = '-';
        $quantidade = $_POST['quantidade'];
        $data_recebimento = $this->_getParam('data_recebimento');
        $data_aprovacao = $this->_getParam('data_aprovacao');
        $gerente_responsavel = $this->_getParam('gerente_responsavel');

        if ($_POST['solicitacaoid']) {
            $solicitacaoid = $_POST['solicitacaoid'];
        } else {
            $solicitacaoid = $this->_getParam('solicitacaoid');
        }



        $tsolicitacao = new Solicitacao();
        if ($status == 'entregue') {
            $statusatual = 'entregue';
        } elseif ($status == 'recebida') {
            $statusatual = 'recebida';
        } elseif ($status == 'liberada') {
            $statusatual = 'liberada';
        } elseif ($status == 'pendente') {
            $statusatual = 'pendente';
        } else {

            $statusatual = $tsolicitacao->mostrarStatusAtual($solicitacaoid)->current()->status;
        }

        //aqui ele tira do estoque SOMENTE quando a solicitação é realizada (status nova)
        if ($statusatual == 'nova') {
            if ($produtos[0]) {

                try {
                    $tProduto = new Produto();
                    $tProduto->atualizarEstoque($produtos, $operacao, $quantidade, $_POST['solicitacaoid']);

                    $tProdutosolicitacao = new Produtosolicitacao();
                    $tProdutosolicitacao->registrarQuantidadeDoProdutoNaSolicitacao($produtos, $quantidade, $solicitacaoid);
                } catch (Exception $e) {
                    
                }
            }
        }

        $tsolicitacao->atualizarStatus($solicitacaoid, $statusatual, $gerente_responsavel);

        if ($data_recebimento) {
            $tsolicitacao->atualizaDataDeRecebimento($solicitacaoid, $data_recebimento);
        }
        if ($data_aprovacao) {
            $tsolicitacao->atualizaDataDeaprovação($solicitacaoid, $data_aprovacao);
        }

        $perfil = Zend_Auth::getInstance()->getIdentity()->perfilid;
        if ($perfil == 1) {
            return $this->_helper->redirector->gotoSimple('listar', 'solicitacao');
        } elseif ($perfil == 2 || $perfil == 3) {
            return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');
        }
    }

    public function resumodesolicitacaoAction() {

        $solicitacaoid = $this->_getParam('solicitacaoid');

        $ProdutoSolicitacao = new Produtosolicitacao();
        $resumoDeSolicitacao = $ProdutoSolicitacao->resumoDeSolicitacao($solicitacaoid);
        
        $this->view->resumoDeSolicitacao = $resumoDeSolicitacao;
        $this->view->solicitacaoid = $solicitacaoid;
    }

    public function limparcarrinhodesolicitacaoAction() {


        $solicitacaoid = $this->_getParam('solicitacaoid');

        $ProdutoSolicitacao = new Produtosolicitacao();
        $ProdutoSolicitacao->limparCarrinhoDeSolicitacao($solicitacaoid);

        return $this->_helper->redirector->gotoSimple('listar', 'solicitacao');
    }

    public function inserirprodutoemsolicitacaoagendadaAction() {

        if (!($_POST)) {

            $produtoId = $this->_getParam("produtoid");

            $this->view->produtoid = $produtoId;
        } else {

            $produtoId = $_POST['produtoid'];
            $quantidade = $_POST['quantidade'];
            $data_agendamento = $_POST['data_agendamento'];

            $tSolicitacao = new Solicitacao();
            $solicitacaoid = $tSolicitacao->selecionarUltimaSolicitacaoCadastrada()->current()->maxID;
            
            $tProdutoSolicitacao = new Produtosolicitacao();
            $tProdutoSolicitacao->inserirProdutoNaSolicitacaoAgendada($solicitacaoid, $produtoId, $quantidade, $data_agendamento);

            return $this->_helper->redirector->gotoSimple('listar', 'solicitacao');
        }
    }

    public function aprovarparcialitemdasolicitacaoAction() {

        if (!($_POST)) {

            $produtoid = $this->_getParam("produtoid");
            $solicitacaoid = $this->_getParam("solicitacaoid");
            $produtosolicitacaoid = $this->_getParam("produtosolicitacaoid");

            $this->view->produtoid = $produtoid;
            $this->view->solicitacaoid = $solicitacaoid;
            $this->view->produtosolicitacaoid = $produtosolicitacaoid;
        } else {

            $produtoid = $_POST['produtoid'];
            $solicitacaoid = $_POST['solicitacaoid'];

            $quantidadeescolhida = $_POST['quantidade'];
            
            $tProdutoSolicitacao = new Produtosolicitacao();
            $produtosolicitacao = $tProdutoSolicitacao->findByProdutoESolicitacao($produtoid, $solicitacaoid);
            
            $tProduto = new DbTable_Produto();
            $produto = $tProduto->find($produtoid)->current();
            $quantidadeCorrente = $produto->quantidade;

            //retorna quantidade solicitada em produtosolicitacao
            $quantidadesolicitada = $produtosolicitacao->current()->quantidade;
            
            //verifica se já existe quantidade aprovada parcial
            $tProdutoSolicitacao = new DbTable_Produtosolicitacao();
            $quantidadeJahAprovada = $tProdutoSolicitacao->quantidadeJahAprovadaParcialPorProdutoESolicitacao($produtoid, $solicitacaoid);
            
            $quantidadetotal = $quantidadeescolhida;
            
            
            if ($quantidadeJahAprovada[0]['aprovacao_parcial'] > 0) {

                $quantidadeAntigaAprovada = $quantidadeJahAprovada[0]['aprovacao_parcial'];
                $quantidadetotal = $quantidadetotal + $quantidadeAntigaAprovada;
            }

            if ($quantidadetotal <= $quantidadesolicitada) {

                $quantidadetotal = $quantidadeescolhida + $quantidadeAntigaAprovada;
                
                $post = array('aprovacao_parcial' => $quantidadetotal);
                
                $tProdutoSolicitacao = new ProdutoSolicitacao();
                $tProdutoSolicitacao->inserirAprovacaoParcial($post, $produtosolicitacao);

                $quantidadeFinal = $quantidadeCorrente + $quantidadetotal - $quantidadeAntigaAprovada;
                
                $tProduto = new Produto();
        
                $tProduto->atualizarEstoqueComProdutoDevolvido($produtoid, $quantidadeFinal, $fail = null);

                $this->flashMessenger->addMessage(array('success' => "A quantidade deste item foi atualizado com sucesso!"));
                return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');
            } else {

                $this->flashMessenger->addMessage(array('danger' => "Quantidades devolvidas não podem ultrapassar a quantidade solicitada para o produto!"));
                return $this->_helper->redirector->gotoSimple('listargerente', 'solicitacao');
            }
        }
    }

}
