<?php

namespace app\common\think\model;

use think\model\Collection as ModelCollection;

/**
 * 数据容器类
 */
class Collection extends ModelCollection
{
    /**
     * 返回数据中指定的一列
     * @access public
     * @param mixed $columnKey 键名
     * @param null  $indexKey  作为索引值的列
     * @return array
     */
    public function column($columnKey, $indexKey = null)
    {
        // 修改 数据做数组转换
        $item = $this->toArray();

        if (function_exists('array_column')) {
            return array_column($item, $columnKey, $indexKey);
        }

        $result = [];
        foreach ($item as $row) {
            $key    = $value = null;
            $keySet = $valueSet = false;

            if (null !== $indexKey && array_key_exists($indexKey, $row)) {
                $key    = (string) $row[$indexKey];
                $keySet = true;
            }

            if (null === $columnKey) {
                $valueSet = true;
                $value    = $row;
            } elseif (is_array($row) && array_key_exists($columnKey, $row)) {
                $valueSet = true;
                $value    = $row[$columnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $result[$key] = $value;
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}
