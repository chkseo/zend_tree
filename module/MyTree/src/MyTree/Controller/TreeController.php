<?php
namespace MyTree\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MyTree\Entity;
use MyTree\View\Helper;

class TreeController extends AbstractActionController
{

public function indexAction()
    {
		 $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		 $treepost = new \MyTree\Entity\TreePost();
		 $treeCategories = $treepost->getTreeCategories($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
		 

		 
		 $view = new ViewModel(array('post' => $treeCategories));
		 return $view;
    }



    public function addAction()
    {
        $form = new \MyTree\Form\TreePostForm();
        $form->get('submit')->setValue('Добавить');

        $request = $this->getRequest();
        if ($request->isPost()) {
        	
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

                $treepost = new \MyTree\Entity\TreePost();

                $treepost->exchangeArray($form->getData());
				$treepost->setId(0);

                $objectManager->persist($treepost);
                $objectManager->flush();

                $message = 'Ветка дерева успешно добавлена!';
                $this->flashMessenger()->addMessage($message);

                // Redirect to list of blogposts
                return $this->redirect()->toRoute('tree');
            }
            else {
                $message = 'Ошибка добавления!';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }
		$treepost = new \MyTree\Entity\TreePost();
		$listCategories = $treepost->getListCategories($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'), 0);
		
		return array('form' => $form, 'list' => $listCategories);
    }

    public function editAction()
    {
        // Check if id set.
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Tree id doesn\'t set');
            return $this->redirect()->toRoute('tree');
        }

        // Create form.
        $form = new \MyTree\Form\TreePostForm();
        $form->get('submit')->setValue('Сохранить');

        $request = $this->getRequest();
        if (!$request->isPost()) {

            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

            $post = $objectManager
                ->getRepository('\MyTree\Entity\TreePost')
                ->findOneBy(array('id' => $id));

            if (!$post) {
                $this->flashMessenger()->addErrorMessage(sprintf('Tree with id %s doesn\'t exists', $id));
                return $this->redirect()->toRoute('tree');
            }

            // Fill form data.
            $form->bind($post);
			$treepost = new \MyTree\Entity\TreePost();
			$listCategories = $treepost->getListCategories($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'), 0);
			
            return array('form' => $form, 'id' => $id, 'post' => $post, 'list' => $listCategories);
        }
        else {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

                $data = $form->getData();
                $id = $data['id'];
                try {
                    $blogpost = $objectManager->find('\MyTree\Entity\TreePost', $id);
                }
                catch (\Exception $ex) {
                    return $this->redirect()->toRoute('tree', array(
                        'action' => 'index'
                    ));
                }

                $blogpost->exchangeArray($form->getData());

                $objectManager->persist($blogpost);
                $objectManager->flush();

                $message = 'Ветка сохранена!!';
                $this->flashMessenger()->addMessage($message);

                // Redirect to list of blogposts
                return $this->redirect()->toRoute('tree');
            }
            else {
                $message = 'Ошибка сохранения ветки!';
                $this->flashMessenger()->addErrorMessage($message);
                return array('form' => $form, 'id' => $id);
            }
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Tree id doesn\'t set');
            return $this->redirect()->toRoute('tree');
        }

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                try {
                    $treepost = $objectManager->find('MyTree\Entity\TreePost', $id);
                    $objectManager->remove($treepost);
                    $objectManager->flush();
                }
                catch (\Exception $ex) {
                    $this->flashMessenger()->addErrorMessage('Error while deleting data');
                    return $this->redirect()->toRoute('tree', array(
                        'action' => 'index'
                    ));
                }

                $this->flashMessenger()->addMessage(sprintf('Blogpost %d was succesfully deleted', $id));
            }

            return $this->redirect()->toRoute('tree');
        }

        return array(
            'id'    => $id,
            'post' => $objectManager->find('MyTree\Entity\TreePost', $id)->getArrayCopy(),
        );
    }
}