<?php
namespace index\model;
use kjy\framework\Model;
class Link extends Model
{
	public function insertLink($data)
	{
		return $this->insert($data);
	}
}