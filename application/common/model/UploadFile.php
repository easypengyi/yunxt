<?php

namespace app\common\model;

use think\Log;
use think\Request;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 文件 模型
 */
class UploadFile extends BaseModel
{
    // 存储位置-本地
    const STORAGE_LOCAL = 'local';
    // 存储位置-网络文件
    const STORAGE_REMOTE = 'remote';

    //oss节点
    const  ENDPOINT = 'https://ydn-product.oss-cn-hangzhou.aliyuncs.com';

    protected $type = ['is_image' => 'boolean', 'sync' => 'boolean'];

    protected $append = ['url', 'full_url'];

    protected $visible = ['file_id', 'is_image', 'image_width', 'image_height', 'sync'];

    //-------------------------------------------------- 静态方法

    /**
     * 文件查找
     * @param $sha1
     * @return static
     * @throws DbException
     */
    public static function find_file($sha1)
    {
        $where = ['sha1' => $sha1];
        return self::get($where);
    }

    /**
     * 添加网络文件
     * @param $url
     * @return static
     * @throws DbException
     */
    public static function insert_remote_file($url)
    {
        if (empty($url)) {
            return null;
        }


        $model = self::get(['path' => $url]);

        if (!empty($model)) {
            return $model;
        }

        $data = [
            'filesize'     => 0,
            'mimetype'     => '',
            'path'         => $url,
            'sha1'         => '',
            'use_number'   => 0,
            'is_image'     => @exif_imagetype($url) !== false,
            'image_width'  => 0,
            'image_height' => 0,
            'create_time'  => time(),
            'update_time'  => time(),
            'sync'         => false,
            'storage'      => self::STORAGE_REMOTE,
        ];

        Log::error($data);

        return self::create($data);
    }

    /**
     * 加载文件
     * @param $file_id
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function load_file($file_id)
    {
        $model = self::get($file_id, [], [true, null, self::getCacheTag()]);
        if (empty($model)) {
            $data  = [
                'file_id'      => 0,
                'is_image'     => true,
                'image_width'  => 0,
                'image_height' => 0,
                'path'         => NO_IMAGE_URL,
            ];
            $model = new static($data);
        }
        return $model->toArray();
    }

    /**
     * 使用数量增加
     * @param $file_id
     * @throws ThinkException
     */
    public static function use_number_inc($file_id)
    {
        self::where(['file_id' => ['in', $file_id]])->setInc('use_number');
    }

    /**
     * 使用数量减少
     * @param $file_id
     * @throws ThinkException
     */
    public static function use_number_dec($file_id)
    {
        self::where(['file_id' => ['in', $file_id]])->setDec('use_number');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 链接 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        if (stripos($data['path'], 'http') !== false) {
            return $data['path'];
        }

        return self::ENDPOINT.'/' . str_replace(DS, '/', $data['path']);
    }

    /**
     * 完整链接 读取器
     * @param $value
     * @return string
     */
    public function getFullUrlAttr($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        $url = $this->getAttr('url');

        if (stripos($url, 'http') !== false) {
            return $url;
        }

        return  self::ENDPOINT. $url;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
