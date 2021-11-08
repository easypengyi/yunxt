<?php

use helper\HttpHelper;

if (!function_exists('output')) {
    /**
     * 输出数据
     * @param null $data
     * @param null $code
     * @param null $message
     * @param bool $return
     * @return array
     * @throws RuntimeException
     */
    function output($data = null, $code = null, $message = null, $return = false)
    {
        is_null($data) AND $data = new stdClass();
        is_null($code) AND $code = app\common\ResultCode::RES_SUCCESS;
        $message === null AND $message = '';
        empty($message) OR $message = think\Lang::get($message);

        $output = ['code' => $code, 'msg' => $message, 'data' => $data, 'timestamp' => time()];
        $return OR abort(json($output));
        return $output;
    }
}

if (!function_exists('output_error')) {
    /**
     * 输出错误提示
     * @param null $message
     * @param null $code
     * @param bool $return
     * @return array
     * @throws RuntimeException
     */
    function output_error($message = null, $code = null, $return = false)
    {
        is_null($code) AND $code = app\common\ResultCode::RES_PARAMETER_ERR;
        return output(null, $code, $message, $return);
    }
}

if (!function_exists('output_success')) {
    /**
     * 输出成功提示
     * @param null $message
     * @param null $data
     * @param bool $return
     * @return array
     * @throws RuntimeException
     */
    function output_success($message = null, $data = null, $return = false)
    {
        return output($data, null, $message, $return);
    }
}

if (!function_exists('base_url')) {
    /**
     * 路径补全域名
     * @param string $string
     * @return string
     */
    function base_url($string = '')
    {
        return request()->domain() . '/' . $string;
    }
}

if (!function_exists('action_url')) {
    /**
     * 返回操作路径
     * @param string|array $vars
     * @param array        $url_vars
     * @param bool         $suffix
     * @param bool         $domain
     * @return string
     */
    function action_url($vars = '', $url_vars = [], $suffix = true, $domain = false)
    {
        return controller_url([request()->action(), $url_vars], $vars, $suffix, $domain);
    }
}

if (!function_exists('controller_url')) {
    /**
     * 返回控制器路径
     * @param string|array $str
     * @param string|array $vars
     * @param bool         $suffix
     * @param bool         $domain
     * @return string
     */
    function controller_url($str = '', $vars = '', $suffix = true, $domain = false)
    {
        $str = (array) $str;
        $url = array_shift($str);
        empty($url) AND $url = 'index';
        array_unshift($str, request()->controller() . '/' . $url);
        return folder_url($str, $vars, $suffix, $domain);
    }
}

if (!function_exists('folder_url')) {
    /**
     * 返回模块路径
     * @param string|array $str
     * @param string|array $vars
     * @param bool         $suffix
     * @param bool         $domain
     * @return string
     */
    function folder_url($str = '', $vars = '', $suffix = true, $domain = false)
    {
        is_array($vars) AND ksort($vars);

        $str = (array) $str;
        $url = array_shift($str);
        empty($url) AND $url = 'Index/index';
        $url_vars = array_shift($str);
        $url_vars AND $url = HttpHelper::get_url_query($url, $url_vars);
        return url(request()->module() . '/' . $url, $vars, $suffix, $domain);
    }
}

if (!function_exists('imagecreatefrombmp')) {
    /**
     * BMP 创建函数
     * @param string $filename path of bmp file
     * @return resource|boolean
     * @example who use,who knows
     * @author  simon
     */
    function imagecreatefrombmp($filename)
    {
        if (!$f1 = fopen($filename, 'rb')) {
            return false;
        }

        $FILE = unpack('vfile_type/Vfile_size/Vreserved/Vbitmap_offset', fread($f1, 14));
        if ($FILE['file_type'] != 19778) {
            return false;
        }

        $BMP           = unpack(
            'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important',
            fread($f1, 40)
        );
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) {
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        }
        $BMP['bytes_per_pixel']  = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal']            = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal']            -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal']            = 4 - (4 * $BMP['decal']);
        if ($BMP['decal'] == 4) {
            $BMP['decal'] = 0;
        }

        $PALETTE = [];
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }

        $IMG  = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P   = 0;
        $Y   = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 32) {
                    $COLOR = unpack('V', substr($IMG, $P, 3));
                    $B     = ord(substr($IMG, $P, 1));
                    $G     = ord(substr($IMG, $P + 1, 1));
                    $R     = ord(substr($IMG, $P + 2, 1));
                    $color = imagecolorexact($res, $R, $G, $B);
                    if ($color == -1) {
                        $color = imagecolorallocate($res, $R, $G, $B);
                    }
                    $COLOR[0] = $R * 256 * 256 + $G * 256 + $B;
                    $COLOR[1] = $color;
                } elseif ($BMP['bits_per_pixel'] == 24) {
                    $COLOR = unpack('V', substr($IMG, $P, 3) . $VIDE);
                } elseif ($BMP['bits_per_pixel'] == 16) {
                    $COLOR    = unpack('n', substr($IMG, $P, 2));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR    = unpack('n', $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack('n', $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0) {
                        $COLOR[1] = ($COLOR[1] >> 4);
                    } else {
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack('n', $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0) {
                        $COLOR[1] = $COLOR[1] >> 7;
                    } elseif (($P * 8) % 8 == 1) {
                        $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                    } elseif (($P * 8) % 8 == 2) {
                        $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                    } elseif (($P * 8) % 8 == 3) {
                        $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                    } elseif (($P * 8) % 8 == 4) {
                        $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                    } elseif (($P * 8) % 8 == 5) {
                        $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                    } elseif (($P * 8) % 8 == 6) {
                        $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                    } elseif (($P * 8) % 8 == 7) {
                        $COLOR[1] = ($COLOR[1] & 0x1);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else {
                    return false;
                }
                imagesetpixel($res, $X, $Y, $COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP['decal'];
        }
        fclose($f1);

        return $res;
    }
}

if (!function_exists('file_get_contents_no_ssl')) {
    /**
     * 文件读取 禁止ssl
     * @param $filename
     * @return false|string
     */
    function file_get_contents_no_ssl($filename)
    {
        return file_get_contents(
            $filename,
            false,
            stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ],
                ]
            )
        );
    }
}

if (!function_exists('exif_imagetype')) {
    /**
     * 判断是否图片类型
     * @param $filename
     * @return bool
     */
    function exif_imagetype($filename)
    {
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
            return $type;
        }
        return false;
    }
}