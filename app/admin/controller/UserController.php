<?php
namespace admin\controller;
use admin\controller\BaseController;
use admin\model\User;

class UserController extends BaseController
{
	protected $selectUser;
	public function _init()
	{
		$this->selectUser = new User();
	}
	public function login()
	{
		$this->display();
	}
	public function checklogin()
	{
		if(isset($_POST)) {
			$username = $_POST['username'];
			$password = md5($_POST['password']);
			$result = $this->selectUser->selectCheck("username='$username' and password ='$password' and udertype=0");
			if ($result) {
				$_SESSION['id'] = $result[0]['uid'];
				$this->notice('登录成功', 'index.php?m=admin&c=index&a=index');
			} else {
				$this->notice('登录失败','');
			}
		}
	}
	public function logout()
	{
		unset($_SESSION['id']);
		$this->notice('退出成功', 'index.php?c=user&m=admin&a=login');
	}
}