<?php

namespace app\api\controller;

use app\common\controller\ApiController;

/**
 * 文件上传 API
 */
class File extends ApiController
{
    /**
     * 图片上传接口
     * @return void
     */
    public function upload_image()
    {
        $this->check_login();

        $result = $this->upload_thumb(false);
        $result['status'] OR output_error($result['message']);

        $id  = [];
        $url = [];
        foreach ($result['data'] as $v) {
            $id[]  = $v['file_id'];
            $url[] = $v['full_url'];
        }
        output_success('', ['image' => implode(',', $id), 'url' => $url]);
    }
}
