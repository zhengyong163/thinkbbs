<?php

namespace app\common\model;

use think\Model;
use app\common\validate\Reply as Validate;
use app\common\exception\ValidateException;

class Reply extends Model
{
    protected static function init()
    {
        static::observe(\app\common\observer\Reply::class);
    }

    // belongs to user
    public function user()
    {
        return $this->belongsTo('User');
    }

    // belongs to category
    public function topic()
    {
        return $this->belongsTo('Topic');
    }

    /**
     * 自定义查询方法
     * @Author   zhanghong(Laifuzi)
     * @param    array              $param    请求参数
     * @param    int                $per_page 每页显示记录数量
     * @return   Paginator                    分页查询结果
     */
    public static function minePaginate($param, $per_page = 10)
    {

        $static = static::order('id', 'DESC');

        foreach ($param as $name => $value) {
            switch ($name) {
                case 'topic_id':
                    $static = $static->with('user')->where($name, intval($value));
                    break;
                case 'user_id':
                    $static = $static->with('topic')->where($name, intval($value));
                    break;
            }
        }
        return $static->paginate($per_page);
    }

    /**
     * 创建回复
     * @Author   zhanghong(Laifuzi)
     * @param    array              $data 表单提交数据
     * @return   Reply
     */
    public static function createItem($data)
    {
        $validate = new Validate;
        if (!$validate->batch(true)->check($data)) {
            $e = new ValidateException('数据验证失败');
            $e->setData($validate->getError());
            throw $e;
        }

        try {
            $reply = new static;
            $reply->allowField(['topic_id', 'user_id', 'content'])->save($data);
        } catch (\Exception $e) {
            throw new \Exception('创建回复失败');
        }

        return $reply;
    }
}
