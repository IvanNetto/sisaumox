<?php
class DbTable_Usuario extends Zend_Db_Table_Abstract
{

	protected $_name = 't_usuario';
	protected $_rowClass = 'Usuario';
        protected $_primary = 'id';

}