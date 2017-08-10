<?php
namespace index\model;
use kjy\framework\Model;
class Blog extends Model
{
	public function selectBlog($ch,$where,$limit)
	{
		if ($ch == 1) {
			return $this->limit('3')->order('posts desc,replycount desc')->select();
		} elseif ($ch == 2) {
			return $this->select();
		} elseif ($ch == 3) {
			return $this->where($where)->select();
		} elseif ($ch == 4) {
			return $this->field('c.username,c.picture,a.*')->table('bk_blog as a left join bk_user as c on a.uid=c.uid')->where($where)->select();

		} elseif ($ch == 5) {
			return $this->where($where)->limit($limit)->select();
		}
	}
	public function updateInc($field,$where,$value)
	{
		return $this->where($where)->setInc($field,$value);
	}
	public function insertReply($data)
	{
		return $this->insert($data);
	}
}