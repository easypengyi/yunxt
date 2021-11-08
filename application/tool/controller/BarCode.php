<?php

namespace app\tool\controller;

use Exception;
use think\Cache;
use think\Config;
use app\common\core\Common;
use Picqer\Barcode\BarcodeGeneratorJPG;

/**
 * 条形码
 */
class BarCode extends Common
{
    protected $name_prefix = '';

    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->name_prefix = Config::get('bar_code.prefix');
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
            $generator = new BarcodeGeneratorJPG();
            $result    = $generator->getBarcode($data, $generator::TYPE_CODE_128);
            Cache::set($name, $result);
        }
        return response($result, 200, ['content-type' => 'image/jpg']);
    }
}
