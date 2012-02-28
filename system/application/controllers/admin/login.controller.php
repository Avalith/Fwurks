<?php
class Login_Controller extends Admin_Controller
{
	public $__view = 'login';
	
	public function index()
	{
		if($this->is_post())
		{
			$user = AdminUser::loadSessionUser($_POST['username'], $_POST['password']);
			
			if(isset($user) && $user->id)
			{
				$this->__session->logged_user = $user;
				
				$redirect = $this->__session->after_login_redirect;
				unset($this->__session->after_login_redirect);
				
				redirect($redirect);
			}
			else
			{
				$this->error = 1;
				$this->username = $_POST['username']; 
			}
		}
	}
	
	public function logout()
	{
		unset($this->__session->logged_user);
		redirect('//login');
	}
}

?>
