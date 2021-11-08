<?php

namespace app\tool\controller;

use Exception;
use think\Config;
use think\File;
use think\Image;
use app\common\core\Common;

/**
 * 缩略图
 */
class Thumb extends Common
{
    protected $path = '';

    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->check_referer()) {
            $this->error('无访问权限！');
        }

        $this->path = Config::get('thumb.path');
    }

    /**
     * 默认方法
     * @return mixed
     */
    public function index()
    {
        $data            = input('data', '');
        $thumb_file_name = $this->path . md5($data);
        if (!is_file($thumb_file_name)) {
            try {
                // 初始化文件夹
                is_dir($this->path) OR mkdir($this->path, 0777, true);
                // 文件加载
                $file = new File(PUBLIC_PATH . $data);
                if ($file->getSize() > 32768) {
                    $image = Image::open($file);
                    $image->thumb(100, 100);
                    $image->save($thumb_file_name);
                } else {
                    copy(PUBLIC_PATH . $data, $thumb_file_name);
                }
            } catch (Exception $e) {
                $thumb_file_name = base_url(NO_IMAGE_URL);
            }
        }

        return response(file_get_contents_no_ssl($thumb_file_name), 200, ['content-type' => 'image/jpg']);
    }
}
