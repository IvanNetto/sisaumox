<?php

class Solicitacao extends Zend_Db_Table_Row_Abstract {

    public function findSolicitacoesAtivasByUsuario($usuarioid, $solicitacoesAtivas) {

        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('usuarioid = (?)', $usuarioid)
                ->where('status in (?)', $solicitacoesAtivas);

        return $tSolicitacao->fetchAll($query);
    }

    public function verificarSeExisteSolicitacaoNova($usuarioId) {
        
        $statusnova = ['nova', 'em analise'];

        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('usuarioid = (?)', $usuarioId)
                ->where('status in (?)', $statusnova);
        
        return $tSolicitacao->fetchAll($query);
    }

    public function inserirSolicitacao($post, $usuarioId) {

        $jahExisteSolicitacao = $this->verificarSeExisteSolicitacaoNova($usuarioId);

        if ($jahExisteSolicitacao->current() <> null) {

            throw new exception("Você já possui uma solicitação com status NOVA!");
        } else {
            $tSolicitacao = new DbTable_Solicitacao();
            $novaSolicitacao = $tSolicitacao->createRow();
            $novaSolicitacao->setFromArray($post);
            $novaSolicitacao->save();
        }
    }

    public function mostrarStatusAtual($solicitacaoid) {
        //refatorar para chamar somente a coluna status
        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('id = (?)', $solicitacaoid);

        return $tSolicitacao->fetchAll($query);
    }

    public function atualizarStatus($idSolicitacao, $statusatual) {

        
        switch ($statusatual) {
            case 'nova':
                $status = 'em analise';
                break;
            case 'em analise':
                $status = 'entregue';
                break;
            case 'rejeitada':
                $status = 'nova';
                break;
            case 'entregue':
                $status = 'recebida';
                break;
            case 'agendada':
                $status = 'aguardando fornecedor';
                break;
            case 'aguardando fornecedor':
                $status = 'entregue';
                break;
            default:
                $status = 'recebida';
                break;
        }

        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($idSolicitacao);

        $dados = array('status' => $status);

        $solicitacao->current()->setFromArray($dados);
        $solicitacao->current()->save();
    }

    public function listarHistorico($usuarioId) {

        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('usuarioid = (?)', $usuarioId)
                ->where('status = (?)', 'recebida');

        return $tSolicitacao->fetchAll($query);
    }
    
    public function atualizaDataDeRecebimento($idSolicitacao,$data_solicitacao){
        
        
        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($idSolicitacao);
        
        $post = ['data_recebimento' => $data_solicitacao];
        $solicitacao->current()->setFromArray($post);
        $solicitacao->current()->save();
        
        
    }

}
