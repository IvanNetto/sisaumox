<?php

class Solicitacao extends Zend_Db_Table_Row_Abstract {

    public function findSolicitacoesAtivasByUsuario($usuarioid, $solicitacoesAtivas) {

        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('usuarioid = (?)', $usuarioid)
                ->where('status in (?)', $solicitacoesAtivas);

        return $tSolicitacao->fetchAll($query);
    }

    public function inserirSolicitacao($post) {

            $tSolicitacao = new DbTable_Solicitacao();
            $novaSolicitacao = $tSolicitacao->createRow();
            $novaSolicitacao->setFromArray($post);
            $novaSolicitacao->save();
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
            case 'reprovada':
                $status = 'reprovada';
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
            case 'cancelada':
                $status = 'cancelada';
                break;
            case 'aprovada':
                $status = 'aprovada';
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

        $lista = ['recebida', 'reprovada', 'cancelada'];
        
        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('usuarioid = (?)', $usuarioId)
                ->where('status in (?)', $lista); 

        return $tSolicitacao->fetchAll($query);
    }
    
    
    public function listarAgendadas(){
        
        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('status = (?)', 'agendada'); 

        return $tSolicitacao->fetchAll($query);
        
        
        
    }
    
    public function atualizaDataDeRecebimento($idSolicitacao,$data_recebimento){
        
        
        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($idSolicitacao);
        
        $post = ['data_recebimento' => $data_recebimento];
        $solicitacao->current()->setFromArray($post);
        $solicitacao->current()->save();
        
    }
    
    
    public function atualizaDataDeEnvio($idSolicitacao,$data_envio){
        
        $tSolicitacao = new DbTable_Solicitacao();
        $solicitacao = $tSolicitacao->find($idSolicitacao);
        
        $post = ['data_envio' => $data_envio];
        $solicitacao->current()->setFromArray($post);
        $solicitacao->current()->save();
        
        
    }
    
    public function selecionarUltimaSolicitacaoCadastrada(){
        
         $tSolicitacao = new DbTable_Solicitacao();
         $query = $tSolicitacao->select()
                ->from($tSolicitacao, array(new Zend_Db_Expr("MAX(id) AS maxID")));
               
        return $tSolicitacao->fetchAll($query);
        
    }
    
    public function listarSolicitacoesGerente(){
        
         $solicitacoesAtivas = ['em analise','aprovada', 'reprovada', 'agendada', 'aguardando fornecedor'];
        
        $tSolicitacao = new DbTable_Solicitacao();
        $query = $tSolicitacao->select()
                ->where('status in (?)', $solicitacoesAtivas); 

        return $tSolicitacao->fetchAll($query);
        
    }


}
