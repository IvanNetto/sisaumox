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

        $produtoid = $this->_getParam("id");


        $mensagem = array();
        $params = array('categoriaid' => $categoriaid, 'solicitacaoid' => $solicitacaoid, $mensagem);

        try {

            $tProdutosolicitacao = new Produtosolicitacao();
            $novoprodutosolicitacao = $tProdutosolicitacao->inserirProdutoSolicitacao($solicitacaoid, $produtosescolhidos);
            $mensagem = $this->flashMessenger->addMessage(array('success' => "Movido com sucesso para o carrinho de solicitações"));
        } catch (Exception $e) {

            $mensagem = $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));
        };

        return $this->forward('buscarprodutosporcategoria', 'produtosolicitacao', null, $params);
    }

    public function deletaritemdocarrinhoAction() {

        $solicitacaoid = $this->_getParam('solicitacaoid');
        $produtoid = $this->_getParam('produtoid');

        $tProdutosolicitacao = new Produtosolicitacao();
        $produtosolicitacao = $tProdutosolicitacao->findByProdutoESolicitacao($solicitacaoid, $produtoid);

        $mensagem = array();

        try {

            $produtosolicitacao->current()->delete();
            $mensagem = $this->flashMessenger->addMessage(array('success' => "Item removido do carrinho com sucesso"));
        } catch (Exception $e) {

            $mensagem = $this->flashMessenger->addMessage(array('success' => "Bem, isto é constrangedor! Algo aconteceu com seu item. Recarregue a página (pressione F5 do seu teclado) e tente novamente"));
        }

        return $this->forward('carrinhodesolicitacoes', 'produtosolicitacao', null, ['solicitacaoid' => $solicitacaoid], $mensagem);
    }

    public function carrinhodesolicitacoesAction() {

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
        $data_envio = $this->_getParam('data_envio');

        if ($_POST['solicitacaoid']) {

            $solicitacaoid = $_POST['solicitacaoid'];
        } else {
            $solicitacaoid = $this->_getParam('solicitacaoid');
        }


        if ($produtos) {

            try{
                $tProduto = new Produto();
                $mensagem = $tProduto->atualizarEstoque($produtos, $operacao, $quantidade);
                
                $tProdutosolicitacao = new Produtosolicitacao();
                $tProdutosolicitacao->registrarQuantidadeDoProdutoNaSolicitacao($produtos, $quantidade, $solicitacaoid);

                //mensagem de estoque minimo atingido
                if ($mensagem){

                    $this->flashMessenger->addMessage(array('danger' => $mensagem));

                }

            }catch (Exception $e){
                //gera exceção se solicicitar quantidade superior a existente em estoque
                $this->flashMessenger->addMessage(array('danger' => $e->getMessage()));

            }
        }

        $tsolicitacao = new Solicitacao();
        $statusatual = $tsolicitacao->mostrarStatusAtual($solicitacaoid)->current()->status;
        $atualizaSolicitacao = $tsolicitacao->atualizarStatus($solicitacaoid, $statusatual);

        if ($data_recebimento) {

            $atualizaSolicitacao = $tsolicitacao->atualizaDataDeRecebimento($solicitacaoid, $data_recebimento);
        }
        
        if ($data_envio) {

            $atualizaSolicitacao = $tsolicitacao->atualizaDataDeEnvio($solicitacaoid, $data_envio);
        
            
        }


        //como eu mando pra solicitacao/listar?
        return $this->_helper->redirector->gotoSimple('listar', 'solicitacao');
    }

    public function resumodesolicitacaoAction() {

        $solicitacaoid = $this->_getParam('solicitacaoid');

        $ProdutoSolicitacao = new Produtosolicitacao();
        $resumoDeSolicitacao = $ProdutoSolicitacao->resumoDeSolicitacao($solicitacaoid);

        $this->view->resumoDeSolicitacao = $resumoDeSolicitacao;
    }

    public function limparcarrinhodesolicitacaoAction() {


        $solicitacaoid = $this->_getParam('solicitacaoid');

        $ProdutoSolicitacao = new Produtosolicitacao();
        $carrinhocancelado = $ProdutoSolicitacao->limparCarrinhoDeSolicitacao($solicitacaoid);

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
            $novoprodutosolicitacao = $tProdutoSolicitacao->inserirProdutoNaSolicitacaoAgendada($solicitacaoid, $produtoId, $quantidade, $data_agendamento);
        }
    }

}
