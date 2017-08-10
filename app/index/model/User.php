<?php
namespace index\model;
use kjy\framework\Model;
class User extends Model
{
	public function selectCheck($where)
	{
		return $this->where($where)->select();
	}
	public function insertUser($data)
	{
		return $this->insert($data);
	}
	public function upInfo($where,$data)
	{
		return $this->where($where)->update($data);
	}
}