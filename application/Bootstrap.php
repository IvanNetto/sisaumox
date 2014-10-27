<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoload() {

        set_time_limit(600000);
        ini_set("memory_limit", "2000M");

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        $default_loader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
        return $default_loader;
    }

    protected function _initBancoDados() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        $db = Zend_Db::factory($config->db);

        $profiler = new Zend_Db_Profiler_Firebug('ALL DB QUERYES');
        $profiler->setEnabled(true);
        $db->setProfiler($profiler);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    protected function _initViews() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        Zend_Registry::set('view', $view);
        $view->headTitle()->setSeparator(' - ')->headTitle('SISAUMOX');
        $view->headMeta()->appendHttpEquiv('Content-type', 'text/html;charset=UTF-8');
    }

}
