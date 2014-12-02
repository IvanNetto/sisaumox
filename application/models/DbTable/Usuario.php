<?php

class DbTable_Usuario extends Zend_Db_Table_Abstract {

    protected $_name = 't_usuario';
    protected $_rowClass = 'Usuario';
    protected $_primary = 'id';

    public function perfilUsuario($usuario) {

        $sql = "select setor.nome as setor, funcao.nome as funcao, perfil.nome as perfil, pessoa.nome as pessoa, usuario.login as login 
                    from t_setor setor, t_funcao funcao, t_perfil perfil, t_pessoa pessoa, t_usuario usuario
                    where pessoa.id = usuario.pessoaid
                    and setor.id = funcao.setorid
                    and funcao.id = pessoa.funcaoid
                    and perfil.id = usuario.perfilid
                    and usuario.id = $usuario";

        return $this->getAdapter()->fetchAll($sql);
    }

    
    public function usuariosPorPerfil($perfil){
        
        $sql = "select * from t_usuario where perfil = '$perfil' order by login";
        
        return $this->getAdapter()->fetchAll($sql);
        
    }
}
