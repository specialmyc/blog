<?php
namespace index\controller;
use index\controller\BaseController;
use index\model\Blog;
use index\model\Link;
use kjy\framework\Page;

class IndexController extends BaseController
{
	protected $link;
	protected $sblog;
	protected $upinc;
	protected $inreply;
	public function _init()
	{
		$this->link = new Link();
		$this->sblog = new Blog();
		$this->upinc = new Blog();
		$this->inreply = new Blog();
	}
	public function index()
	{
		$data = $this->sblog->selectBlog(1,'','');
		$result1 = $this->sblog->selectBlog(3,'first=0','');
		$tot = count($result1);
		$setpage = new Page($tot,2);
		$limit = $setpage->limit();
		$result = $this->sblog->selectBlog(5,'first=0',$limit);
		$page = $setpage->allPage();
		$this->assign('page',$page);
		$this->assign('data',$data);
		$this->assign('result',$result);
		$this->assign('result1',$result1);
		$this->display();
	}
	public function about()
	{
		$this->display();
	}
	public function contact()
	{
		$this->display();
	}
	public function insmessage()
	{
		if (empty($_SESSION['uid'])) {
			$this->notice('请登录');
			exit;
		}
		if (!empty($_POST)) {
			$name = $_POST['name'];
			$email = $_POST['email'];
			$enquiry = $_POST['message'];
			if (empty($name) || empty($email)|| empty($enquiry)) {
				$this->notice('不能为空');
				exit;
			}
			if(!preg_match("/(\w)+@(\w)+(\.(com|cn|net))$/", $email)) {
				$this->notice('注册失败，邮箱格式错误');
				exit;
			}
			$data = [
				'name' => $name,
				'email' => $email,
				'message' => $enquiry,
			];
			$result = $this->link->insertLink($data);
		}
		if ($result) {
			$this->notice('添加成功');
		} else {
			$this->notice('添加失败', '',111111);
		}
	}
	public function a()
	{
		$result = $this->sblog->selectBlog(3,'first=0','');
		$this->assign('result',$result);
		$this->display();
	}
	public function b()
	{
		$this->display();
	}
	public function blog()
	{
		$bid = $_GET['bid'];
		$this->upinc->updateInc('posts',"bid=$bid",'1');
		$result = $this->sblog->selectBlog(3,"bid=$bid",'');
		$data = $this->sblog->selectBlog(4,"a.first=1 and tid=$bid",'');
		$this->assign('result',$result);
		$this->assign('data',$data);
		$this->display();
	}
	public function reply()
	{
		if (empty($_SESSION['uid'])) {
			$this->notice('请登录');
			exit;
		}
		if (!empty($_POST['send']) && !empty($_POST['reply'])) {
			$tid = $_GET['bid'];
			$uid = $_SESSION['uid'];
			$content = $_POST['reply'];
			if($_SERVER['REMOTE_ADDR'] == '::1'){
				$ip = ip2long('127.0.0.1');
			}else{
				$ip = ip2long($_SERVER['REMOTE_ADDR']);
			}
			$data = [
				'uid' => $uid,
				'first' => 1,
				'tid' => $tid,
				'content' => $content,
				'addtime' => time(),
				'addip' => $ip
			];
			$result = $this->inreply->insertReply($data);
			if ($result) {
				$this->upinc->updateInc('replycount',"bid=$tid",'1');
				$this->notice('回复成功');
			} else {
				$this->notice('回复失败');
			}
		}
	}
}