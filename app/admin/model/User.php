<?php
namespace admin\model;
use kjy\framework\Model;
class User extends Model
{
	public function selectCheck($where)
	{
		return $this->where($where)->select();
	}
	public function selectall($ch,$limit)
	{
		if ($ch==1) {
			return $this->select();
		} elseif ($ch==2) {
			return $this->limit($limit)->select();
		}
	}
	public function upInfo($where,$data)
	{
		return $this->where($where)->update($data);
	}
	public function deleteUser($where)
	{
		return $this->where($where)->delete();
	}
}