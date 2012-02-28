<?php

class Login_Controller extends Admin_Controller
{
	public $__layout = 'login';
	
	public function index()
	{
		if(is_post())
		{
			$user = new AdminUser();
			$user = $user->loadSessionUser($_POST['username'], $_POST['password']);
			
			if($user->id)
			{
				$this->__session->logged_user = $user;
				redirect('//');
			} else { $this->error = 1; $this->username = $_POST['username']; }
		}
	}
	
	public function logout()
	{
		unset($this->__session->logged_user);
		redirect(array(':controller' => 'login'));
	}
}

?>