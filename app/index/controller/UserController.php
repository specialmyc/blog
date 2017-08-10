<?php
namespace index\controller;
use index\controller\BaseController;
use index\model\User;
use kjy\framework\VerifyCode;
use kjy\framework\Upload;
use vendor\alidayu\TopClient;
use vendor\alidayu\AlibabaAliqinFcSmsNumSendRequest;

class UserController extends BaseController
{
	protected $ver;
	protected $selectUser;
	protected $inUser;
	protected $upinfo;
	public function _init()
	{
		$this->ver = new VerifyCode();
		$this->selectUser = new User();
		$this->inUser = new User();
		$this->upinfo = new User();
	}
	public function login()
	{
		$this->display();
	}
	public function logincheck()
	{
		if(isset($_POST)) {
			$username = $_POST['username'];
			$password = md5($_POST['password']);
			$result = $this->selectUser->selectCheck("username='$username' and password ='$password'");
			if ($result) {
				$this->notice('登录成功', 'index.php?c=index&m=index&a=index');
				$_SESSION['uid'] = $result[0]['uid'];
				$_SESSION['username'] = $result[0]['username'];
				$_SESSION['udertype'] = $result[0]['udertype'];
				$_SESSION['email'] = $result[0]['email'];
				$_SESSION['picture'] = $result[0]['picture'];
			} else {
				$this->notice('登录失败');
			}
		}
	}

	public function logout()
	{
		unset($_SESSION['uid'],$_SESSION['username'],$_SESSION['udertype'],$_SESSION['email'],$_SESSION['picture']);
		$this->notice('退出成功','index.php?c=index&m=index&a=index');
	}

	public function picture()
	{
		$this->display();
	}

	public function updatepic()
	{
		$file = new Upload();
		$picture = $file->upload('picture');
		$uid = $_SESSION['uid'];
		$data = ['picture' => $picture];
		$result = $this->upinfo->upInfo("uid='$uid'",$data);
		$_SESSION['picture']=$picture;
		if ($result) {
			$this->notice('上传成功','index.php?c=index&m=index&a=index');
		} else {
			$this->notice('上传失败');
		}
	}

	public function register()
	{
		$this->display();
	}
	public function registercheck()
	{
		$code = $_SESSION['code'];
		$uname = trim($_POST['username']);
		$upass = trim($_POST['password']);
		$urpass = trim($_POST['repwd']);
		$phone = (int)trim($_POST['phone']);
		$umail = trim($_POST['email']);
		$yzm = $_POST['code'];
		
		//$id = $_POST['id'];
		if($_SERVER['REMOTE_ADDR'] == '::1'){
			$ip = ip2long('127.0.0.1');
		}else{
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
		}
		//用户名不能为空
		if(empty($uname))
		{
			$this->notice('注册失败，用户名为空');
			exit;
		}
		//判断用户名长度
		if(strlen($uname) < 3 or strlen($uname) > 12)
		{
			$this->notice('注册失败，用户名错误');
			exit;
		}
		$create_time = time();
		//判断用户名是否已经存在
		$exists = $this->selectUser->selectCheck("username='$uname'");
		if(!empty($exists))
		{
			$this->notice('注册失败，用户名已存在');
			exit;
		}
		//密码是否合法
		if(strlen($upass) < 3 or strlen($upass) > 12)
		{
			$this->notice('注册失败，密码格式错误');
			exit;
		}
		//比较两次密码是否一致
		if($upass !== $urpass)
		{
			$this->notice('注册失败，两次密码不一致');
			exit;
		}
		if(!preg_match("/^1[34578]\d{9}$/", $phone)) {
			$this->notice('注册失败，手机格式错误');
			exit;
		}
		//判断邮箱是否合法
		if(!preg_match("/(\w)+@(\w)+(\.(com|cn|net))$/", $umail))
		{
			$this->notice('注册失败，邮箱格式错误');
			exit;
		}
		//判断验证码是否正确
		if($code != $yzm)
		{
			$this->notice('注册失败，验证码错误');
			exit;
		}
		$upass = md5($upass);
		//用户创建
		$data = [
				'username' 	=> $uname,
				'password' 	=> $upass,
				'email'     => $umail,
				'phone'     => $phone,
				'regip'     => $ip,
				'regtime' => time(),
				'udertype' => 1,
				];
		$result = $this->inUser->insertUser($data);
		if ($result) {
			$result = $this->selectUser->selectCheck("username='$uname'");
			$this->notice('注册成功', 'index.php?c=index&m=index&a=index');
			$_SESSION['uid'] = $result[0]['uid'];
			$_SESSION['username'] = $result[0]['username'];
			$_SESSION['udertype'] = $result[0]['udertype'];
			$_SESSION['email'] = $result[0]['email'];
			$_SESSION['picture'] = $result[0]['picture'];
		} else {
			$this->notice('注册失败');
		}
	}
	public function ver()
	{
		$this->ver->outputImage();
		$_SESSION['code'] = $this->ver->getCode();
	}
	public function findpwd()
	{
		$this->display();
	}
	public function sendSMS()
	{
		$tel = $_POST['mobile'];//手机号
		              
		$c = new TopClient;//大于客户端   
		$c->format = 'json';//设置返回值得类型

		$c->appkey = "24454345";//阿里大于注册应用的key

	    $c->secretKey = "2313ee517157e1bd62c2ee849977672a";//注册的secretkey
	                                                       
	    //请求对象，需要配置请求的参数   
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend("123456");//公共回传参数，可以不传
		$req->setSmsType("normal");//短信类型，传入值请填写normal
		
		//签名，阿里大于-控制中心-验证码--配置签名 中配置的签名，必须填
		$req->setSmsFreeSignName("马永昌");
		
		//你在短信中显示的验证码，这个要保存下来用于验证
		$num = rand(100000,999999);
		$_SESSION['smscode'] = $num;

		//短信模板变量，传参规则{"key":"value"}，key的名字须和申请模板中的变量名一致，传参时需传入{"code":"1234","product":"alidayu"}
		$req->setSmsParam("{\"number\":\"$num\"}");//模板参数
		                                           
		//短信接收号码。
	     $req->setRecNum($tel);

		//短信模板。阿里大于-控制中心-验证码--配置短信模板 必须填
		$req->setSmsTemplateCode("SMS_71371005");
		$resp = $c->execute($req);//发送请求
		return $resp;
		
	}
	public function findcheck()
	{
		$username = $_POST['username'];
		$phone = $_POST['phone'];
		$code = $_POST['code'];
		$yzm = $_SESSION['smscode'];
		$result = $this->selectUser->selectCheck("username='$username' and phone =$phone");
		
		$uid = $result[0]['uid'];
		if (($code==$yzm)&&($result)) {
			$this->notice("信息正确，请修改密码","index.php?m=index&c=user&a=updatepwd&uid=$uid");
		} else {
			$this->notice("信息错误");
		}
	}
	public function updatepwd()
	{
		$uid = $_GET['uid'];
		$this->assign('uid',$uid);
		$this->display();
	}
	public function uppwd()
	{
		$uid = $_GET['uid'];
		if (empty($uid)) {
			$this->notice("修改失败",'index.php');
			exit;
		}
		if (empty($_POST)) {
			$this->notice("修改失败",'index.php');
			exit;
		}
		$pwd = $_POST['password'];
		$repwd = $_POST['repwd'];
		if ($pwd !== $repwd) {
			$this->notice("修改失败",'index.php');
			exit;
		}
		$data = ['password' => md5($pwd)];
		$result = $this->upinfo->upInfo("uid='$uid'",$data);
		if ($result) {
			$this->notice("修改成功",'index.php?m=index&c=user&a=login');
		} else {
			$this->notice("修改失败",'index.php');
			exit;
		}
	}
}