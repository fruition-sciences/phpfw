<?php
/**
 * Created on Apr 17 2015
 * @author: Sidiki Coulibaly
 */
set_include_path(dirname(__FILE__)."/../ZendFramework/library:" . get_include_path());
require_once dirname(__FILE__). '/../ZendFramework/library/Zend/Loader/AutoloaderFactory.php';
require_once dirname(__FILE__). '/../ZendFramework/library/Zend/Loader/ClassMapAutoloader.php';
\Zend_Loader_AutoloaderFactory::factory(
		array(
				'Zend_Loader_ClassMapAutoloader' => array(
                    dirname(__FILE__) . '/tests/config/autoload_classmap_zend.php',
                    dirname(__FILE__) . '/tests/config/autoload_classmap_phpfw.php'
				)
		)
);