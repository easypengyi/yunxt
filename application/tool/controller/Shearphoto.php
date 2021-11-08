<?php

namespace app\tool\controller;

use Exception;
use think\File;
use tool\UploadTool;
use app\common\think\Image;
use app\common\core\Common;

/**
 * 图片处理
 */
class Shearphoto extends Common
{
    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->check_referer()) {
            $this->error('无访问权限！');
        }
    }

    /**
     * 上传信息
     * @return mixed
     */
    public function upload()
    {
        $result = UploadTool::instance()->upload_thumb();
        if (!$result['status']) {
            return json_encode(['erro' => $result['message']]);
        }

        return json_encode(['success' => $result['data']['url']]);
    }

    /**
     * 图片提交
     * @return string
     * @throws Exception
     */
    public function image_submit()
    {
        $JSdate = input('JSdate', '');

        if (empty($JSdate)) {
            /** @var $file File[]|File */
            $file = request()->file();
            is_array($file) AND $file = array_shift($file);
            $image = Image::open($file->getInfo('tmp_name'));
        } else {
            $JSdate = json_decode(stripslashes($JSdate), true);
            $image  = Image::open(PUBLIC_PATH . $JSdate['url']);
            $image->rotate($JSdate['R'], false);
            $image->crop($JSdate['IW'], $JSdate['IH'], $JSdate['X'], $JSdate['Y']);
        }

        $uploadfile = UploadTool::instance()->image_save($image);

        return json_encode($uploadfile);
    }
}
