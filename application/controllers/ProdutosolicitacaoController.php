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

        if ($this->_getParam('deucerto') == true) {

            $this->flashMessenger->addMessage(array('success' => "Movido com sucesso para o carrinho de solicitações"));
        }

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
        $produtosolicitacao = $tProdutosolicitacao->findByProdutoESolicitacao($solicitacaoid, $produtoid);

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

        $produtos = $_POST['produto'];
        $operacao = '-';
        $quantidade = $_POST['quantidade'];
        $data_recebimento = $this->_getParam('data_recebimento');
        $data_aprovacao = $this->_getParam('data_aprovacao');
        $gerente_responsavel = $this->_getParam('gerente_responsavel');

        //mudar
        $status = $this->_getParam('status');

        if ($_POST['solicitacaoid']) {

            $solicitacaoid = $_POST['solicitacaoid'];
        } else {
            $solicitacaoid = $this->_getParam('solicitacaoid');
        }


        if ($produtos) {

            try {
                $tProduto = new Produto();
                $mensagem = $tProduto->atualizarEstoque($produtos, $operacao, $quantidade);

                $tProdutosolicitacao = new Produtosolicitacao();
                $tProdutosolicitacao->registrarQuantidadeDoProdutoNaSolicitacao($produtos, $quantidade, $solicitacaoid);

                //mensagem de estoque minimo atingido
                if ($mensagem) {

                    $this->flashMessenger->addMessage(array('danger' => $mensagem));
                }
            } catch (Exception $e) {
                //gera exceção se solicicitar quantidade superior a existente em estoque
                $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
                $qtdsuperior = true;
            }
        }

        $tsolicitacao = new Solicitacao();
        if ($status == 'entregue') {
            $statusatual = 'entregue';
        } elseif ($status == 'recebida') {
            $statusatual = 'recebida';
        } else {
            $statusatual = $tsolicitacao->mostrarStatusAtual($solicitacaoid)->current()->status;
        }

        if (!($qtdsuperior))
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

    public function devolveritemdasolicitacaoAction() {


        if (!($_POST)) {

            $produtoid = $this->_getParam("produtoid");
            $solicitacaoid = $this->_getParam("solicitacaoid");
            $produtosolicitacaoid = $this->_getParam("produtosolicitacaoid");
            $status = $this->_getParam("status");

            $this->view->produtoid = $produtoid;
            $this->view->solicitacaoid = $solicitacaoid;
            $this->view->produtosolicitacaoid = $produtosolicitacaoid;
            $this->view->status = $status;
        } else {

            $produtoid = $_POST['produtoid'];
            $produtosolicitacaoid = $_POST['produtosolicitacaoid'];
            $solicitacaoid = $_POST['solicitacaoid'];
            $quantidadeescolhida = $_POST['quantidade'];
            $observacao = $_POST['observacao'];
            $data = $_POST['data'];
            $status = $_POST['status'];

            $tProdutoSolicitacao = new Produtosolicitacao();
            $produtocolicitacao = $tProdutoSolicitacao->findByProdutoESolicitacao($solicitacaoid, $produtoid);

            //retorna quantidade solicitada em produtosolicitacao
            $quantidadesolicitada = $produtocolicitacao->current()->quantidade;

            //verifica se já existe quantidade devolvida
            $tDevolucao = new DbTable_Devolucao();
            $devolucao = $tDevolucao->somaDeQuantidadeTotalDevolvidaPorProdutoSolicitacao($produtoid);

            if ($devolucao) {

                $quantidadeAntigaDevolvida = $devolucao[0]['quantidade'];
                $quantidadetotal = $quantidadeescolhida + $quantidadeAntigaDevolvida;
            }

            if ($quantidadetotal <= $quantidadesolicitada) {

                $post = array('produtosolicitacaoid' => $produtosolicitacaoid, 'data_devolucao' => $data, 'quantidade_devolvida' => $quantidadeescolhida, 'observacao' => $observacao, 'status_devolucao' => $status);

                $tDevolucao = new Devolucao();
                $tDevolucao->inserirDevolucao($post);

                $this->flashMessenger->addMessage(array('success' => "Este item de sua solicitação foi devolvido com sucesso!"));
            } else {

                $this->flashMessenger->addMessage(array('danger' => "Quantidades devolvidas não podem ultrapassar a quantidade solicitada para o produto!"));
            }

            return $this->_helper->redirector->gotoSimple('listarhistorico', 'solicitacao');
        }
    }

    public function listardevolucaoAction() {

        $tDevolucao = new DbTable_Devolucao();
        $listaDeDevolucoes = $tDevolucao->listarNovasDevolucoes();

        $this->view->listaDeDevolucoes = $listaDeDevolucoes;
    }

    public function atualizardevolucaoAction() {
        $id = $this->_getParam("id_devolucao");
        $status = $this->_getParam("status");
        $gerenteAprovador = $this->_getParam("gerente_responsavel");
        
        $post = ['status_devolucao' => $status, 'gerente_responsavel' => $gerenteAprovador];

        $tDevolucao = new Devolucao();
        $devolucaoAtualizada = $tDevolucao->atualizarStatusDevolucao($id, $post);
        
        
        $tDevolucao = new DbTable_Devolucao;
        $produtoDevolvido = $tDevolucao->findProdutoByDevolucao($id);
        
        if ($status == 'entregue'){
            
            $tProduto = new Produto();
            $tProduto->devolverItemProEstoque($produtoDevolvido[0]['id'], $id);
            
        }
        
        return $this->_helper->redirector->gotoSimple('listardevolucao', 'produtosolicitacao');
        
        
    }
    
    
    public function listardevolucaoantigaAction(){
       
        $usuarioId = Zend_Auth::getInstance()->getIdentity()->id;
        $status = 'recebida';
        

        $tSolicitacao = new Solicitacao;
        $solicitacao = $tSolicitacao->findSolicitacoesAtivasByUsuario($usuarioId, $status);
        var_dump($solicitacao);die;

        $tProdutosolicitacao = new Produtosolicitacao;
        $produtoSolicitacao = $tProdutosolicitacao->findBySolicitacao($solicitacao->current()->id);
        


        $tDevolucao = new DbTable_Devolucao();
        $devolucoesAntigas = $tDevolucao->listarDevolucoesAntigas($produtoSolicitacao->current()->id);
        
        $this->view->devolucoesantigas = $devolucoesAntigas;
        $this->view->usuarioid = $usuarioId;

        
    }

}
