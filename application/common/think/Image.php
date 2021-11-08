<?php

namespace app\common\think;

use SplFileInfo;
use think\Image as ThinkImage;
use think\image\Exception as ImageException;

/**
 * 图片处理类
 */
class Image extends ThinkImage
{
    /**
     * 打开一个图片文件
     * @param SplFileInfo|string $file
     * @return Image
     */
    public static function open($file)
    {
        if (is_string($file)) {
            $file = new SplFileInfo($file);
        }
        if (!$file->isFile()) {
            throw new ImageException('image file not exist');
        }
        return new static($file);
    }

    /**
     * 保存图像
     * @param string      $pathname  图像保存路径名称
     * @param null|string $type      图像类型
     * @param int         $quality   图像质量
     * @param bool        $interlace 是否对JPEG类型图像设置隔行扫描
     * @return $this
     */
    public function save($pathname, $type = null, $quality = 80, $interlace = true)
    {
        $this->checkPath(dirname($pathname));
        parent::save($pathname, $type, $quality, $interlace);

        return $this;
    }

    /**
     * 旋转图像
     * @param int  $degrees 顺时针旋转的度数
     * @param bool $Clockwise
     * @return static
     */
    public function rotate($degrees = 90, $Clockwise = true)
    {
        $Clockwise OR $degrees = -$degrees;

        parent::rotate($degrees);

        return $this;
    }

    /**
     * 检查目录是否可写
     * @access protected
     * @param  string $path 目录
     * @return void
     */
    protected function checkPath($path)
    {
        if (is_dir($path) || mkdir($path, 0755, true)) {
            return;
        }

        throw new ImageException('directory ' . $path . ' creation failed');
    }
}
