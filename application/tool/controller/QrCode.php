<?php

namespace app\tool\controller;

use Exception;
use think\Cache;
use think\Config;
use app\common\core\Common;
use Endroid\QrCode\QrCode as EndroidQrCode;

/**
 * 二维码
 */
class QrCode extends Common
{
    protected $name_prefix = '';

    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->name_prefix = Config::get('qr_code.prefix');
    }

    /**
     * 默认方法
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $data   = input('data', '');
        $name   = $this->name_prefix . md5($data) . strlen($data);
        $result = Cache::get($name);
        if (!$result) {
            $qrCode = new EndroidQrCode($data);
            $result = [
                'image'        => $qrCode->writeString(),
                'content-type' => $qrCode->getContentType(),
            ];
            Cache::set($name, $result);
        }
        return response($result['image'], 200, ['content-type' => $result['content-type']]);
    }
}
