<?php
namespace admin\model;
use kjy\framework\Model;
class Blog extends Model
{
	public function insertBlog($data)
	{
		return $this->insert($data);
	}
	public function selectBlog($ch,$limit)
	{
		if ($ch==1) {
			return $this->where('first=0')->select();
		} elseif ($ch==2) {
			return $this->limit($limit)->where('first=0')->select();
		}
	}
	public function selectmBlog($ch,$limit)
	{
		if ($ch == 1) {
			return $this->field('c.username,a.*,b.title')->table('bk_blog as a left join bk_blog as b on a.tid=b.bid left join bk_user as c on a.uid=c.uid')->where("a.first=1")->select();
		} elseif ($ch == 2) {
			return $this->field('c.username,a.*,b.title')->table('bk_blog as a left join bk_blog as b on a.tid=b.bid left join bk_user as c on a.uid=c.uid')->where("a.first=1")->limit($limit)->select();
		}

	}
	public function selectUpBlog($where)
	{
		return $this->where($where)->select();
	}
	public function deleteBlog($where)
	{
		return $this->where($where)->delete();
	}
	public function upBlog($where,$data)
	{
		return $this->where($where)->update($data);
	}
	public function delRe($where)
	{
		return $this->where($where)->delete();
	}
}