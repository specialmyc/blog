<?php
//写命名空间 模块名\\congroller
namespace admin\controller;
use kjy\framework\Template;//引入名空间
date_default_timezone_set('PRC');

class BaseController extends Template
{
	public function __construct()
	{
		//$this->tplDir = $this->checkDir('app/index/view');
		//$this->cacheDir = $this->checkDir('cache/index');
		parent::__construct('cache/admin','app/admin/view');
		$this->_init();
	}
	//子类的初始化方法
	public function _init()
	{
		if (empty($_SESSION['id'])) {
			$this->notice("未登录",'index.php?m=admin&c=user&a=login');
		}
	}

	public function display($viewFile=null,$isExtract=true)
	{
		if (empty($viewFile)) {
			$viewFile = $_GET['c'].'/'.$_GET['a'].'.html';
		}
		parent::display($viewFile,$isExtract);
	}

	public function notice($con, $url = null, $sec = 3)
	{
		if (empty($url)) {
			$url = $_SERVER['HTTP_REFERER'];
		}
		$this->assign('con', $con);
		$this->assign('url', $url);
		$this->assign('sec', $sec);
		$this->display('notice.html');
	}
}
