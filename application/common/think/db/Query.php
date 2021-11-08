<?php

namespace app\common\think\db;

use Exception;
use think\Cache;
use think\Config;
use think\Paginator;
use think\db\Builder;
use think\db\Query as BaseQuery;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;
use think\db\exception\BindParamException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;

/**
 * 查询类
 */
class Query extends BaseQuery
{
    /** @var Builder */
    protected $builder;

    /**
     * 分页查询
     * @param int|array $listRows 每页数量 数组表示配置参数
     * @param int|bool  $simple   是否简洁模式或者总记录数
     * @param array     $config   配置参数
     *                            page:当前页,
     *                            path:url路径,
     *                            query:url额外参数,
     *                            fragment:url锚点,
     *                            var_page:分页变量,
     *                            list_rows:每页数量
     *                            type:分页类名
     *                            paginate:是否分页
     * @return Paginator
     * @throws DbException
     * @throws ThinkException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function paginate($listRows = null, $simple = false, $config = [])
    {
        if (is_int($simple)) {
            $total  = $simple;
            $simple = false;
        }
        if (is_array($listRows)) {
            $config   = array_merge(Config::get('paginate'), $listRows);
            $listRows = $config['list_rows'];
        } else {
            $config   = array_merge(Config::get('paginate'), $config);
            $listRows = $listRows ?: $config['list_rows'];
        }

        /** @var Paginator $class */
        if (false !== strpos($config['type'], '\\')) {
            $class = $config['type'];
        } else {
            $class = '\\think\\paginator\\driver\\' . ucwords($config['type']);
        }

        $paginate = isset($config['paginate']) ? boolval($config['paginate']) : true;

        if ($paginate) {
            if (isset($config['page'])) {
                $page = (int) $config['page'];
            } else {
                $page = call_user_func([$class, 'getCurrentPage'], $config['var_page']);
            }

            $page = $page < 1 ? 1 : $page;
        } else {
            $page = 1;
        }

        $config['path'] = isset($config['path']) ? $config['path'] : call_user_func([$class, 'getCurrentPath']);

        if (!$paginate) {
            $simple   = false;
            $results  = $this->select();
            $total    = $results->count();
            $listRows = $total + 1;
        } elseif (!isset($total) && !$simple) {
            $options = $this->getOptions();

            unset($this->options['order'], $this->options['limit'], $this->options['page'], $this->options['field']);

            $bind    = $this->bind;
            $total   = $this->count();
            $results = $this->options($options)->bind($bind)->page($page, $listRows)->select();
        } elseif ($simple) {
            $results = $this->limit(($page - 1) * $listRows, $listRows + 1)->select();
            $total   = null;
        } else {
            $results = $this->page($page, $listRows)->select();
        }
        return $class::make($results, $listRows, $page, $total, $simple, $config);
    }

    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @return integer|string
     * @throws PDOException
     * @throws ThinkException
     * @throws BindParamException
     */
    public function update(array $data = [])
    {
        $options = $this->parseExpress();
        $data    = array_merge($options['data'], $data);
        $pk      = $this->getPk($options);
        if (isset($options['cache']) && is_string($options['cache']['key'])) {
            $key = $options['cache']['key'];
        }

        if (empty($options['where'])) {
            // 如果存在主键数据 则自动作为更新条件
            if (is_string($pk) && isset($data[$pk])) {
                $where[$pk] = $data[$pk];
                if (!isset($key)) {
                    $key = 'think:' . $options['table'] . '|' . $data[$pk];
                }
                unset($data[$pk]);
            } elseif (is_array($pk)) {
                // 增加复合主键支持
                foreach ($pk as $field) {
                    if (isset($data[$field])) {
                        $where[$field] = $data[$field];
                    } else {
                        // 如果缺少复合主键数据则不执行
                        throw new ThinkException('miss complex primary data');
                    }
                    unset($data[$field]);
                }
            }
            if (!isset($where)) {
                // 如果没有任何更新条件则不执行
                throw new ThinkException('miss update condition');
            } else {
                $options['where']['AND'] = $where;
            }
        } elseif (!isset($key) && is_string($pk) && isset($options['where']['AND'][$pk])) {
            try {
                $key = $this->getCacheKey($options['where']['AND'][$pk], $options, $this->bind);
            } catch (ThinkException $e) {
            }
        }

        // 生成UPDATE SQL语句
        $sql = $this->builder->update($data, $options);
        // 获取参数绑定
        $bind = $this->getBind();
        if ($options['fetch_sql']) {
            // 获取实际执行的SQL语句
            return $this->connection->getRealSql($sql, $bind);
        } else {
            // 检测缓存
            if (isset($key) && Cache::get($key)) {
                // 删除缓存
                Cache::rm($key);
            } elseif (!empty($options['cache']['tag'])) {
                Cache::clear($options['cache']['tag']);
            }
            // 执行操作
            $result = '' == $sql ? 0 : $this->execute($sql, $bind);
            if ($result) {
                if (is_string($pk) && isset($where[$pk])) {
                    /** @var array $where */
                    $data[$pk] = $where[$pk];
                } elseif (is_string($pk) && isset($key) && strpos($key, '|')) {
                    list(, $val) = explode('|', $key);
                    $data[$pk] = $val;
                }
                $options['data'] = $data;
                $this->trigger('after_update', $options);
            }
            return $result;
        }
    }

    /**
     * 生成缓存标识
     * @access public
     * @param mixed $value   缓存数据
     * @param array $options 缓存参数
     * @param array $bind    绑定参数
     * @return string
     * @throws ThinkException
     */
    protected function getCacheKey($value, $options, $bind = [])
    {
        if (is_scalar($value)) {
            $data = $value;
        } elseif (is_array($value) && is_string($value[0]) && 'eq' == strtolower($value[0])) {
            $data = $value[1];
        }
        $prefix = $this->connection->getConfig('database') . '.';

        if (isset($data)) {
            // 修改 增加 查询条件
            return 'think:' . $prefix .
                (is_array($options['table']) ? key($options['table']) : $options['table']) .
                '|' . $data . '|' . serialize($options['where']);
        }

        try {
            return md5($prefix . serialize($options) . serialize($bind));
        } catch (Exception $e) {
            throw new ThinkException('closure not support cache(true)');
        }
    }
}
