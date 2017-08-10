<?php
namespace admin\model;
use kjy\framework\Model;
class Link extends Model
{
	public function selectLink($ch,$limit)
	{
		if ($ch == 1) {
			return $this->select();
		} elseif ($ch == 2) {
			return $this->limit($limit)->select();
		}
		
	}
	public function delLink($where)
	{
		return $this->where($where)->delete();
	}
}