<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'my_tree' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/MyTree/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'MyTree\Entity' => 'my_tree',
                )
            )
        )
    ),
    
	'controllers' => array(
	    'invokables' => array(
	        'MyTree\Controller\TreePost' => 'MyTree\Controller\TreeController',
	    ),
	),

	'view_helpers' => array(
	    'invokables' => array(
	        'showMessages' => 'MyTree\view\Helper\ShowMessages',
	    ),
	),
	
	'router' => array(
	    'routes' => array(
	        'tree' => array(
	            'type'    => 'segment',
	            'options' => array(
	                'route'    => '/tree[/][:action][/:id]',
	                'constraints' => array(
	                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
	                    'id'     => '[0-9]+',
	                ),
	                'defaults' => array(
	                    'controller' => 'MyTree\Controller\TreePost',
	                    'action'     => 'index',
	                ),
	            ),
	        ),
	    ),
	),
	

	
	'view_manager' => array(
	    'template_path_stack' => array(
	        __DIR__ . '/../view',
	    ),
	),
	
);