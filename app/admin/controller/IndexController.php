<?php
namespace admin\controller;
use admin\controller\BaseController;
use admin\model\User;
use admin\model\Blog;
use admin\model\Link;
use kjy\framework\Upload;
use kjy\framework\Page;

class IndexController extends BaseController
{
	protected $selectUser;
	protected $selectAll;
	protected $uppass;
	protected $delUser;
	protected $inblog;
	protected $seblog;
	protected $delblog;
	protected $selectupblog;
	protected $updateb;
	protected $slink;
	protected $dlink;
	protected $sereply;
	protected $dreply;
	public function _init()
	{
		$this->selectUser = new User();
		$this->selectAll = new User();
		$this->uppass = new User();
		$this->delUser = new User();
		$this->inblog = new Blog();
		$this->seblog = new Blog();
		$this->delblog = new Blog();
		$this->selectupblog = new Blog();
		$this->updateb = new Blog();
		$this->sereply = new Blog();
		$this->slink = new Link();
		$this->dlink = new Link();
		$this->dreply = new Blog();
	}
	public function index()
	{
		if (!empty($_SESSION['id'])) {
			$this->display();
		}
	}
	public function uppwd()
	{
		$this->display();
	}
	public function uppassword()
	{
		$uid = $_SESSION['id'];
		$result = $this->selectUser->selectCheck("uid = $uid");
		if ($result[0]['password'] != md5($_POST['mpass'])) {
			$this->notice('原始密码错误');
		}
		if (!$_POST['newpass']==md5($_POST['renewpass'])) {
			$this->notice('两次密码不一致');
		}
		$pass = md5($_POST['newpass']);
		$result = $this->uppass->upInfo("uid = $uid",['password' => $pass]);
		if (!$result) {
			$this->notice('修改失败');
		} else {
			$this->notice('修改成功');
		}
	}
	public function usermanage()
	{
		$result = $this->selectAll->selectall(1,'');
		$tot = count($result);
		$setpage = new Page($tot,2);
		$limit = $setpage->limit();
		$result = $this->selectAll->selectall(2,$limit);
		$page = $setpage->allPage();
		$this->assign('page',$page);
		$this->assign('result',$result);
		$this->display();
	}
	public function usercon()
	{
		if (!empty($_GET['uid'])) {
			$uid = $_GET['uid'];
			$this->delUser->deleteUser("uid = '$uid'");
			$this->notice('删除成功');
		}
		if (!empty($_POST['deletes'])) {
			if (!empty($_POST['uid'])) {
				$uid = $_POST['uid'];
				$uid = join(',', $uid);
				$this->delUser->deleteUser("uid in ($uid)");
			} else {
				$this->notice('请选择后，再删除博文');
				exit;
			}
			$this->notice('删除成功');
		}
	}
	public function postblog()
	{
		$this->display();
	}
	public function postb()
	{
		$file = new Upload();
		$picture = $file->upload('picture');
		$title = $_POST['title'];
		$content = $_POST['content'];
		$uid = $_SESSION['id'];
		if($_SERVER['REMOTE_ADDR'] == '::1'){
			$ip = ip2long('127.0.0.1');
		}else{
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
		}
		if (empty($title) || empty($content)) {
			$this->notice('标题或内容不能为空');
		}
		$data = [
				'uid' => $uid,
				'first' => 0,
				'tid' => 0,
				'title' => $title,
				'content' => addslashes($content),
				'addtime' => time(),
				'addip' => $ip,
				'picture'=>$picture
		];
		$result = $this->inblog->insertBlog($data);
		if ($result) {
			$this->notice('发表成功');
		} else {
			$this->notice('发表失败');
		}
	}
	public function blogmanage()
	{
		$data = $this->seblog->selectBlog(1,'');
		$tot = count($data);
		$setpage = new Page($tot,5);
		$limit = $setpage->limit();
		$data = $this->seblog->selectBlog(2,$limit);
		$page = $setpage->allPage();
		$this->assign('page',$page);
		$this->assign('data',$data);
		$this->display();
	}
	public function delblog()
	{
		if (!empty($_GET['bid'])) {
			$bid = $_GET['bid'];
			$this->delblog->deleteBlog("bid = '$bid'");
			$this->notice('删除成功');
		}
		if (!empty($_POST['deletes'])) {
			if (!empty($_POST['bid'])) {
				$bid = $_POST['bid'];
				$bid = join(',', $bid);
				$this->delblog->deleteBlog("bid in ($bid)");
			}
			$this->notice('删除成功');
		} else {
			$this->notice('删除失败');
		}
	}
	public function upblog()
	{
		$bid = $_GET['bid'];
		$result = $this->selectupblog->selectUpBlog("bid=$bid");
		$this->assign('result',$result);
		$this->display();
	}
	public function updateblog()
	{
		$bid = $_GET['bid'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$data = [
			'title' => $title,
			'content' => addslashes($content),
		];
		$result = $this->updateb->upBlog("bid=$bid",$data);
		if (!$result) {
			$this->notice('修改失败');
		} else {
			$this->notice('修改成功');
		}
	}
	public function pass()
	{
		$this->display();
	}
	
	public function adv()
	{
		$this->display();
	}
	public function replymanage()
	{
		$result = $this->sereply->selectmBlog(1,'');
		$tot = count($result);
		$setpage = new Page($tot,5);
		$limit = $setpage->limit();
		$result = $this->sereply->selectmBlog(2,$limit);
		$page = $setpage->allPage();
		$headpage = $page['first'];
		$lastpage = $page['last'];
		$prevpage = $page['pre'];
		$nextpage = $page['next'];
		$this->assign('headpage',$headpage);
		$this->assign('lastpage',$lastpage);
		$this->assign('prevpage',$prevpage);
		$this->assign('nextpage',$nextpage);
		$this->assign('result',$result);
		$this->display();
	}
	public function delreply()
	{
		if (!empty($_GET['bid'])) {
			$bid = $_GET['bid'];
			$this->dreply->delRe("bid = '$bid'");
			$this->notice('删除成功');
			exit;
		}
		if (!empty($_POST['deletes'])) {
			if (!empty($_POST['bid'])) {
				$bid = $_POST['bid'];
				$bid = join(',', $bid);
				$this->dreply->delRe("bid in ($bid)");
			}
			$this->notice('删除成功');
			exit;
		} else {
			$this->notice('删除失败');
			exit;
		}
	}
	public function linkmanage()
	{
		$result = $this->slink->selectLink(1,'');
		$tot = count($result);
		$setpage = new Page($tot,2);
		$limit = $setpage->limit();
		$result = $this->slink->selectLink(2,$limit);
		$page = $setpage->allPage();
		$headpage = $page['first'];
		$lastpage = $page['last'];
		$prevpage = $page['pre'];
		$nextpage = $page['next'];
		$this->assign('headpage',$headpage);
		$this->assign('lastpage',$lastpage);
		$this->assign('prevpage',$prevpage);
		$this->assign('nextpage',$nextpage);
		$this->assign('result',$result);
		$this->display();
	}
	public function dellink()
	{
		if (!empty($_GET['lid'])) {
			$lid = $_GET['lid'];
			$this->dlink->delLink("lid = '$lid'");
			$this->notice('删除成功');
			exit;
		}
		if (!empty($_POST['deletes'])) {
			if (!empty($_POST['lid'])) {
				$lid = $_POST['lid'];
				$lid = join(',', $lid);
				$this->dlink->delLink("lid in ($lid)");
			}
			$this->notice('删除成功');
			exit;
		} else {
			$this->notice('删除失败');
			exit;
		}
	}
}