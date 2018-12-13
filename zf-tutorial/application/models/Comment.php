<?php
class Comment {
			public function show($newest){ 
				if($newest == 1) $newest = 'DESC'; else $newest='ASC'; 
				if(isset($_GET['page'])){				//	Навигация по страницам
					$messageId = $_GET['page'];
				} else $messageId = 1;
				$messageId *= 5;
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = new Zend_Db_Select($db);
				$select->from('user', array('name', 'created_at')) 
				->joinInner('message',
				'user.user_id = message.user_id',
				 array('title','text'))
				->limit(5,$messageId-5)
				->order('message_id '.$newest.'');
				return $resultSet = $db->fetchAll($select);
			}
			public function insert($data){
				$error='';
				if ($data['name'] == '' || $data['email'] == '' || $data['title'] == '' || $data['text'] == '') {
							$error = 'Не заполнены все поля';
						}
					elseif (strpos($data['name'],'+')){
							$error = 'Имя содержит символ "+"';
						}
					elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
							$error = 'Введите существующий email';
					}
				if ($error ==''){	
							$data_user = array('name'=>$data['name'],'email'=>$data['email'],
							'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'],'created_at'=>date("Y-m-d H:i:s",strtotime('+2 hours')));
							$data_message = array('title'=>$data['title'],'text'=>$data['text']);
							$db = Zend_Db_Table::getDefaultAdapter();	
							$email_search = new Zend_Db_Select($db); //Проверка на наличие email
							$email_search= $db->select()
								->from('user')
								->where('email = ?', $data_user['email']);
							$email_search_result=$db->fetchAll($email_search);
							if (!$email_search_result) {
								$db->insert('user', $data_user); //Если совпадения email нету, добавляем нового пользователя		
							}
							$email_search_result=$db->fetchAll($email_search);  //Снова выполняем поиск, но он уже даст 100% результат
						  $data_message['user_id'] = $email_search_result[0]['user_id'];
							$db->insert('message', $data_message);
							return;
					}
					else {
						$data['error'] = $error;
						return $data;
					}
			}
}