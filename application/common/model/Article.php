<?php

namespace app\common\model;

use helper\HttpHelper;
use think\Request;
use helper\StrHelper;
use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 文章 模型
 */
class Article extends BaseModel
{
    protected $type = ['content' => 'base64'];

    protected $file = ['file_ids' => ['file', true]];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 文章信息
     * @param      $article_id
     * @param bool $replace
     * @return static
     * @throws DbException
     */
    public static function info($article_id, $replace = false)
    {
        $model = self::get($article_id);
        if (empty($model)) {
            return null;
        }

        if ($replace) {
            $content = StrHelper::html_replace_img($model->getAttr('content'), Request::instance()->domain());
            $model->setAttr('content', $content);
        }

        return $model;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法
    /**
     * 商品详情链接 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getDetailUrlAttr($value, $data)
    {
        $this->hidden(['detail_id']);

        if (!is_null($value)) {
            return $value;
        }

        return HttpHelper::article_url($data['detail_id']);
    }


    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}