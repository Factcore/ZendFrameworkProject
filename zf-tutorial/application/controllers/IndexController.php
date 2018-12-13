<?php
class IndexController extends Zend_Controller_Action
{
			function init()
	{
				$this->view->baseUrl = $this->_request->getBaseUrl();
				Zend_Loader::loadClass('Comment');
	}
			function indexAction()
	{
				session_start();
				$this->view->title = "Комментарии";
				$comment = new Comment();
				if(!isset($_SESSION['newest'])) $_SESSION['newest'] = 1; 
				if(isset($_GET['newest'])) $_SESSION['newest'] = $_GET['newest'];
				$this->view->resultSet = $comment->show($_SESSION['newest']);	//Отображение комментариев по новизне
				if ($this->_request->isPost()) {  //Проверка, был ли отправлен комментарий
						Zend_Loader::loadClass('Zend_Filter_StripTags');
						$filter = new Zend_Filter_StripTags();
						$name = trim($filter->filter($this->_request->getPost('name')));
						$email = trim($filter->filter($this->_request->getPost('email')));
						$title = trim($filter->filter($this->_request->getPost('title')));
						$text = trim($filter->filter($this->_request->getPost('text')));
						$error = '';
						$data = array(
										'name' => $name,
										'email' => $email,
										'title' => $title,
										'text' => $text,
								);		
						if($this->view->data=$comment->insert($data)) return $this->view->data;
						else $this->_redirect('/');		
					}
	}
}