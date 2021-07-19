<?php namespace App\ModelFilters;

use App\Models\Department;
use EloquentFilter\ModelFilter;

/**
 * 用户的查询
 * Class UserFilter
 * @package App\Models\ModelFilters
 */
class UserFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 账号
     * @param $value
     * @return mixed
     */
    public function username($value)
    {
//        $this->input('name');
        return $this->where(function ($query) use ($value) {
            $query->where('nickname', 'like', $value.'%')->orWhere('username', 'like', $value.'%');
        });
    }

    /**
     * 是否在职
     * @param $value
     * @return UserFilter
     */
    public function status($value)
    {
        return $this->where('status', $value);
    }
}
