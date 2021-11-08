<?php

namespace app\tool\controller;

use Exception;
use tool\UploadTool;
use app\common\core\Common;

/**
 * 上传图片
 */
class Upload extends Common
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
     * 多图上传
     * @return mixed
     * @throws Exception
     */
    public function multiple_upload()
    {
        $this->assign('callback', trim(input('callback', '')));
        return $this->fetch();
    }

    /**
     * 单图上传
     * @return mixed
     */
    public function single_upload()
    {
        $this->assign('callback', trim(input('callback', '')));
        return $this->fetch();
    }

    /**
     * 视频上传
     * @return mixed
     */
    public function video_upload()
    {
        $this->assign('callback', trim(input('callback', '')));
        $this->assign('upload_type', trim(input('upload_type', '')));
        return $this->fetch();
    }

    /**
     * 文件上传
     * @return void
     * @throws Exception
     */
    public function file_upload()
    {
        $upload_type = input('upload_type', '');
        switch ($upload_type) {
            case 'video':
                $result = $this->upload_video();
                break;
            case 'audio':
                $result = $this->upload_music();
                break;
            default:
                $result = $this->upload_thumb();
                break;
        }
        $result['status'] OR $this->error($result['message']);
        $this->success_result($result['data']);
    }

    /**
     * 文件检查
     * @throws Exception
     */
    public function file_check()
    {
        $file = input('file_md5');
        $ext  = input('file_ext');

        $result = UploadTool::instance()->trunk_check($file, $ext);
        $this->success_result($result);
    }

    /**
     * 文件合并
     * @throws Exception
     */
    public function file_merge()
    {
        $file = input('file_md5');
        $ext  = input('file_ext');

        $result = UploadTool::instance()->trunk_merge($file, $ext);
        $this->success_result($result);
    }
}
