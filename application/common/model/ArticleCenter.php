<?php

namespace app\common\model;

use helper\HttpHelper;
use app\common\core\BaseModel;
use helper\StrHelper;
use think\exception\DbException;
use think\Request;

/**
 * 文章 模型
 */
class ArticleCenter extends BaseModel
{
    protected $type = ['content' => 'base64'];

    protected $file = ['image_id' => 'image'];

    protected $autoWriteTimestamp = true;

    const TYPE_ONE = 1;//素材
    const TYPE_TWO = 2;//文章

    //-------------------------------------------------- 静态方法


    /**
     * @param $type
     * @return array
     * @throws DbException
     */

    public static function  article_list($type)
    {

        $where['del']        = false;
        $where['enable']     = true;
        $where['type']       = $type;


        $order = ['sort' => 'desc'];

        $list = self::page_list($where,$order);
        if (!$list->isEmpty()) {
            $list->append(['detail_url']);
        }
        return $list->toArray();
    }

    /**
     * 文章信息
     * @param      $article_id
     * @param bool $replace
     * @return static
     * @throws DbException
     */
    public static function info($article_id)
    {
        $model = self::get($article_id);
        if (empty($model)) {
            return null;
        }
        $model->append(['detail_url']);
        return $model;
    }


    public function getDetailUrlAttr($value, $data)
    {
        $this->hidden(['detail_id']);

        if (!is_null($value)) {
            return $value;
        }

        return  file_get_contents(HttpHelper::article_url($data['detail_id']));
    }


    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}