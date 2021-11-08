<?php

namespace app\common\core;

use Closure;
use stdClass;
use Exception;
use think\Model;
use think\Cache;
use think\Loader;
use think\Request;
use think\db\Query;
use think\Paginator;
use think\db\Expression;
use think\model\Relation;
use InvalidArgumentException;
use app\common\model\Configure;
use app\common\model\UploadFile;
use think\exception\DbException;
use think\exception\PDOException;
use paginator\Api as ApiPaginator;
use paginator\Admin as AdminPaginator;
use think\Exception as ThinkException;
use think\db\exception\BindParamException;
use think\model\Collection as ModelCollection;

/**
 * 基础模型
 *
 * @method Query field($field, $except = false, $tableName = '', $prefix = '', $alias = '') static
 * @method Query where($field, $op = null, $condition = null) static
 * @method Query whereTime($field, $op, $range = null) static
 * @method Query order($field, $order = null) static
 * @method Query cache($key = true, $expire = null, $tag = null) static
 * @method Query with($with) static
 * @method Query group($group) static
 * @method Query fetchSql($fetch = true) static
 * @method Query join($join, $condition = null, $type = 'INNER') static
 * @method integer|string insert(array $data = [], $replace = false, $getLastInsID = false, $sequence = null) static
 * @method integer|string insertGetId(array $data, $replace = false, $sequence = null) static
 * @method integer|string insertAll(array $dataSet, $replace = false, $limit = null) static
 * @method boolean chunk($count, $callback, $column = null, $order = 'asc') static
 * @method array column($field, $key = '') static
 * @method integer execute($sql, $bind = []) static
 * @method string getTable($name = '') static
 * @method Expression raw($value) static
 * @method void startTrans() static 启动事务
 * @method void commit() static 用于非自动提交状态下面的查询提交
 * @method void rollback() static 事务回滚
 * @method mixed getTableFields() static 获取当前数据表字段信息
 */
class BaseModel extends Model
{
    protected $type = ['enable' => 'boolean'];

    // 关联模型别名
    protected $relation_alias = [];

    // 上传文件对应字段
    protected $file = [];
    // 头像字段
    protected $file_head = [];

    //-------------------------------------------------- 静态方法

    /**
     * 验证数据是否存在
     * @param            $where
     * @param Query|null $query
     * @return bool
     * @throws ThinkException
     */
    public static function check($where, Query $query = null)
    {
        if (!is_array($where)) {
            $where = [self::primary_key() => $where];
        }
        if (is_null($query)) {
            return static::where($where)->count() != 0;
        }

        return $query->where($where)->count() != 0;
    }

    /**
     * 获取主键
     * @return string|array
     */
    public static function primary_key()
    {
        return (new static())->getPk();
    }

    /**
     * 读取上传文件信息
     * @param $file_id
     * @param $more
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function file_info($file_id, $more = false)
    {
        if ($more) {
            $list = [];
            if (!empty($file_id)) {
                foreach ($file_id as $v) {
                    $list[] = UploadFile::load_file($v);
                }
            }
            return $list;
        }

        return UploadFile::load_file($file_id);
    }

    /**
     * 修改文件ID
     * @param $new_file_id
     * @param $old_file_id
     * @throws ThinkException
     */
    public static function change_file($new_file_id, $old_file_id)
    {
        empty($old_file_id) OR UploadFile::use_number_dec($old_file_id);
        empty($new_file_id) OR UploadFile::use_number_inc($new_file_id);
    }

    /**
     * 数据分页 使用分页对象
     * @param array $where
     * @param array $order
     * @param Query $query
     * @return Paginator|AdminPaginator|ApiPaginator
     * @throws DbException
     */
    public static function page_list($where = [], $order = [], Query $query = null)
    {
        if (is_array($order)) {
            $primary_key = self::primary_key();
            if (!is_array($primary_key) && !isset($order[$primary_key])) {
                $order[$primary_key] = 'asc';
            }
        }

        $paginate = boolval(Request::instance()->param('paginate', 1));

        if (is_null($query)) {
            $query = static::where($where)->order($order);
        } else {
            $query->where($where)->order($order);
        }

        $config = ['query' => Request::instance()->except(['s']), 'paginate' => $paginate];

        $pagesize = max(intval(Request::instance()->param('pagesize', 0)), 0);
        empty($pagesize) OR $config['list_rows'] = $pagesize;

        return $query->paginate($config);
    }

    /**
     * join方法
     * @param        $table
     * @param null   $condition
     * @param Query  $query
     * @param string $type
     * @return Query
     */
    public static function table_join($table, $condition = null, Query $query = null, $type = 'INNER')
    {
        if (is_array($condition)) {
            $result = [];
            foreach ($condition as $k => $v) {
                $result[] = static::table_field($k) . ' = ' . static::table_field($v, $table);
            }
            $condition = $result;
        }
        if (is_null($query)) {
            return self::join($table, $condition, $type);
        }
        return $query->join($table, $condition, $type);
    }

    /**
     * 关联模型join方法
     * @param Relation $relation
     * @param          $table
     * @param          $condition
     * @param string   $type
     * @return Relation
     */
    public static function relation_join(Relation $relation, $table, $condition, $type = 'INNER')
    {
        if (is_array($condition)) {
            $result = [];
            foreach ($condition as $k => $v) {
                $result[] = static::table_field($k, $relation->getTable()) . ' = ' . static::table_field($v, $table);
            }
            $condition = $result;
        }
        return $relation->join($table, $condition, $type);
    }

    /**
     * 给字段加上表名
     * @param        $field
     * @param string $table
     * @return string
     */
    public static function table_field($field, $table = '')
    {
        empty($table) AND $table = static::getTable();
        return $table . '.' . $field;
    }

    /**
     * find_in_set
     * @param        $field
     * @param        $value
     * @param string $logic and or or
     * @return Expression
     */
    public static function find_in_set($field, $value, $logic = 'AND')
    {
        is_array($value) OR $value = explode(',', $value);
        $count = count($value);

        $temp = " find_in_set('%s', $field) ";
        $temp = implode($logic, array_fill(0, $count, $temp));

        return self::raw(vsprintf($temp, $value));
    }

    /**
     * 创建where in子查询语句
     * @param        $sql_where
     * @param string $sql_field
     * @param string $query_field
     * @return Expression
     * @throws DbException
     */
    public static function where_in_raw($sql_where, $sql_field, $query_field = '')
    {
        empty($query_field) AND $query_field = $sql_field;
        $sql = $query_field . ' in ' . static::field($sql_field)->where($sql_where)->buildSql();
        return self::raw($sql);
    }

    /**
     * where not in 子查询语句
     * @param        $sql_where
     * @param        $sql_field
     * @param string $query_field
     * @return Expression
     * @throws DbException
     */
    public static function where_not_in_raw($sql_where, $sql_field, $query_field = '')
    {
        empty($query_field) AND $query_field = $sql_field;
        $sql = $query_field . ' not in ' . static::field($sql_field)->where($sql_where)->buildSql();
        return self::raw($sql);
    }

    /**
     * 经纬度计算
     * @param            $longitude
     * @param            $latitude
     * @param Query|null $query
     * @param string     $longitude_key
     * @param string     $latitude_key
     * @param string     $key
     * @return Query
     */
    public static function latitude_longitude_distance(
        $longitude,
        $latitude,
        Query $query = null,
        $longitude_key = 'longitude',
        $latitude_key = 'latitude',
        $key = 'distance'
    ) {
        $longitude_key = "`{$longitude_key}`";
        $latitude_key  = "`{$latitude_key}`";
        $field         = 'POW(SIN((' . $latitude . '*PI()/180-' . $latitude_key . '*PI()/180)/2),2)+COS(' . $latitude . '*PI()/180)*COS(' . $latitude_key . '*PI()/180)*POW(SIN((' . $longitude . '*PI()/180-' . $longitude_key . '*PI()/180)/2),2)';
        $field         = '6378.138*2*ASIN(SQRT(' . $field . '))*1000';
        $field         = 'ROUND(' . $field . ') AS ' . $key;

        if (is_null($query)) {
            $query = self::field($field);
        }
        return $query->field($field, false);
    }

    /**
     * 添加或者更新
     * @param $insert
     * @param $update
     * @return int
     * @throws Exception
     * @throws DbException
     */
    public static function insert_or_update($insert, $update)
    {
        $insert = static::fetchSql(true)->insert($insert);
        $update = static::fetchSql(true)->where($insert)->update($update);
        $update = preg_replace('/UPDATE.*?SET/', '', $update);
        $arr    = explode('WHERE', $update);
        return static::execute($insert . ' ON DUPLICATE KEY UPDATE ' . $arr[0]);
    }

    /**
     * 查找所有记录
     * @param array|Query $field
     * @param array       $where
     * @param array       $order
     * @param bool        $cache
     * @return static[]|ModelCollection
     * @throws DbException
     */
    public static function all_list($field = [], $where = [], $order = [], $cache = false)
    {
        if ($field instanceof Query) {
            $query = $field->where($where)->order($order);
        } else {
            $query = self::field($field)->where($where)->order($order);
        }

        return static::all($query, [], $cache);
    }

    /**
     * 清空数据表
     * @return int
     * @throws PDOException
     * @throws BindParamException
     */
    public static function truncate_table()
    {
        return static::execute('TRUNCATE TABLE ' . static::getTable());
    }

    //---------------------------------------- 方法重写

    /**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
        parent::init();

        static::afterUpdate(
            function (&$model) {
                /** @var static $model */
                foreach ($model->file as $k => $v) {
                    if (isset($model->data[$k]) && isset($model->origin[$k]) && $model->data[$k] != $model->origin[$k]) {
                        self::change_file($model->data[$k], $model->origin[$k]);
                    }
                }
            }
        );

        static::afterInsert(
            function (&$model) {
                /** @var static $model */
                foreach ($model->file as $k => $v) {
                    if (isset($model->data[$k])) {
                        self::change_file($model->data[$k], 0);
                    }
                }
            }
        );

        static::afterDelete(
            function (&$model) {
                /** @var static $model */
                foreach ($model->file as $k => $v) {
                    if (isset($model->data[$k])) {
                        self::change_file(0, $model->data[$k]);
                    }
                }
            }
        );
    }

    /**
     * 写入数据
     * @access public
     * @param array      $data  数据数组
     * @param array|true $field 允许字段
     * @return static
     */
    public static function create($data = [], $field = null)
    {
        $model = new static();
        if (!empty($field)) {
            $model->allowField($field);
        }
        $result = $model->isUpdate(false)->save($data, []);
        if ($result === false) {
            return null;
        }
        return $model;
    }

    /**
     * 更新数据
     * @access public
     * @param array      $data  数据数组
     * @param array      $where 更新条件
     * @param array|true $field 允许字段
     * @return static
     */
    public static function update($data = [], $where = [], $field = null)
    {
        $model = new static();
        if (!empty($field)) {
            $model->allowField($field);
        }
        $result = $model->isUpdate(true)->save($data, $where);
        if ($result === false) {
            return null;
        }
        return $model;
    }

    /**
     * 分析查询表达式
     * @access public
     * @param mixed      $data  主键列表或者查询条件（闭包）
     * @param string     $with  关联预查询
     * @param array|bool $cache 是否缓存
     * @return Query
     */
    protected static function parseQuery(&$data, $with, $cache)
    {
        if ($data instanceof Query) {
            $result = $data->with($with)->cache(...(array) $cache);
            $data   = null;
        } else {
            $result = static::with($with)->cache(...(array) $cache);
            if (is_array($data)) {
                $key = key($data);
                if ($key !== 0 || ($key === 0 && ($data[$key] instanceof Closure || is_array($data[$key])))) {
                    $result = $result->where($data);
                    $data   = null;
                }
            } elseif ($data instanceof Closure) {
                call_user_func_array($data, [& $result]);
                $data = null;
            }
        }

        return $result;
    }

    //---------------------------------------- 缓存方法

    /**
     * 获取缓存标签
     * @access public
     * @param string $str
     * @return string
     */
    public static function getCacheTag($str = '')
    {
        return static::get_cache_key() . $str;
    }

    /**
     * 地址缓存清理
     * @access public
     * @param string $str
     */
    public static function cacheClear($str = '')
    {
        Cache::clear(static::getCacheTag($str));
    }

    /**
     * 获取缓存关键词
     * @access protected
     * @return string
     */
    protected static function get_cache_key()
    {
        return static::class;
    }

    //-------------------------------------------------- 实例方法

    //---------------------------------------- 方法重写

    /**
     * 初始化模型
     * @access protected
     * @return void
     * @throws DbException
     * @throws ThinkException
     */
    protected function initialize()
    {
        parent::initialize();

        foreach ($this->file as $k => $v) {
            if (is_array($v)) {
                list($name, $more) = $v;
            } else {
                $name = $v;
                $more = false;
            }

            $more AND $this->type[$k] = 'plode';

            if (isset($this->data[$k])) {
                $image = self::file_info($this->getAttr($k), $more);
                if (!$more && in_array($k, $this->file_head) && empty($image['file_id'])) {
                    $image = self::file_info(Configure::getValue('default_head_image'), $more);
                }
                $this->setRelation($name, $image);
                $this->hidden([$k]);
            }
        }
    }

    /**
     * 保存当前数据对象
     * @access public
     * @param array  $data     数据
     * @param array  $where    更新条件
     * @param string $sequence 自增序列名
     * @return integer|false
     * @throws PDOException
     * @throws ThinkException
     */
    public function save($data = [], $where = [], $sequence = null)
    {
        if (is_string($data)) {
            $sequence = $data;
            $data     = [];
        }

        if (!empty($data)) {
            // 数据自动验证
            if (!$this->validateData($data)) {
                return false;
            }
            // 数据对象赋值
            foreach ($data as $key => $value) {
                $this->setAttr($key, $value, $data);
            }
            if (!empty($where)) {
                $this->isUpdate    = true;
                $this->updateWhere = $where;
            }
        }

        // 自动关联写入
        if (!empty($this->relationWrite)) {
            $relation = [];
            foreach ($this->relationWrite as $key => $name) {
                if (is_array($name)) {
                    if (key($name) === 0) {
                        $relation[$key] = [];
                        foreach ($name as $val) {
                            if (isset($this->data[$val])) {
                                $relation[$key][$val] = $this->data[$val];
                                unset($this->data[$val]);
                            }
                        }
                    } else {
                        $relation[$key] = $name;
                    }
                } elseif (isset($this->relation[$name])) {
                    $relation[$name] = $this->relation[$name];
                } elseif (isset($this->data[$name])) {
                    $relation[$name] = $this->data[$name];
                    unset($this->data[$name]);
                }
            }
        }

        // 数据自动完成
        $this->autoCompleteData($this->auto);

        // 事件回调
        if (false === $this->trigger('before_write', $this)) {
            return false;
        }
        $pk = $this->getPk();
        if ($this->isUpdate) {
            // 自动更新
            $this->autoCompleteData($this->update);

            // 事件回调
            if (false === $this->trigger('before_update', $this)) {
                return false;
            }

            // 获取有更新的数据
            $data = $this->getChangedData();

            if (empty($data) || (count($data) == 1 && is_string($pk) && isset($data[$pk]))) {
                // 关联更新
                if (isset($relation)) {
                    $this->autoRelationUpdate($relation);
                }
                return 0;
            } elseif ($this->autoWriteTimestamp && $this->updateTime && !isset($data[$this->updateTime])) {
                // 自动写入更新时间
                $data[$this->updateTime]       = $this->autoWriteTimestamp($this->updateTime);
                $this->data[$this->updateTime] = $data[$this->updateTime];
            }

            if (empty($where) && !empty($this->updateWhere)) {
                $where = $this->updateWhere;
            }

            // 保留主键数据
            foreach ($this->data as $key => $val) {
                if ($this->isPk($key)) {
                    $data[$key] = $val;
                }
            }

            $array = [];

            foreach ((array) $pk as $key) {
                if (isset($data[$key])) {
                    $array[$key] = $data[$key];
                    unset($data[$key]);
                }
            }

            if (!empty($array)) {
                $where = $array;
            }

            // 检测字段
            $allowFields = $this->checkAllowField(array_merge($this->auto, $this->update));

            // 模型更新
            if (!empty($allowFields)) {
                $result = $this->getQuery()->where($where)->strict(false)->field($allowFields)->update($data);
            } else {
                $result = $this->getQuery()->where($where)->update($data);
            }

            // 关联更新
            if (isset($relation)) {
                $this->autoRelationUpdate($relation);
            }

            // 更新回调
            $this->trigger('after_update', $this);

        } else {
            // 自动写入
            $this->autoCompleteData($this->insert);

            // 自动写入创建时间和更新时间
            if ($this->autoWriteTimestamp) {
                if ($this->createTime && !isset($this->data[$this->createTime])) {
                    $this->data[$this->createTime] = $this->autoWriteTimestamp($this->createTime);
                }
                if ($this->updateTime && !isset($this->data[$this->updateTime])) {
                    $this->data[$this->updateTime] = $this->autoWriteTimestamp($this->updateTime);
                }
            }

            if (false === $this->trigger('before_insert', $this)) {
                return false;
            }

            // 检测字段
            $allowFields = $this->checkAllowField(array_merge($this->auto, $this->insert));
            if (!empty($allowFields)) {
                $result = $this->getQuery()->strict(false)->field($allowFields)->insert(
                    $this->data,
                    false,
                    false,
                    $sequence
                );
            } else {
                $result = $this->getQuery()->insert($this->data, false, false, $sequence);
            }

            // 获取自动增长主键
            if ($result && $insertId = $this->getQuery()->getLastInsID($sequence)) {
                foreach ((array) $pk as $key) {
                    if (!isset($this->data[$key]) || '' == $this->data[$key]) {
                        // 自增主键 强制转换为int类型
                        $this->data[$key] = intval($insertId);
                    }
                }
            }

            // 关联写入
            if (isset($relation)) {
                foreach ($relation as $name => $val) {
                    $method = Loader::parseName($name, 1, false);
                    $this->$method()->save($val);
                }
            }

            // 标记为更新
            $this->isUpdate = true;

            // 新增回调
            $this->trigger('after_insert', $this);
        }
        // 写入回调
        $this->trigger('after_write', $this);

        // 重新记录原始数据
        $this->origin = $this->data;

        return $result;
    }

    /**
     * 修改器 设置数据对象值
     * @access public
     * @param string $name  属性名
     * @param mixed  $value 属性值
     * @param array  $data  数据
     * @return $this
     */
    public function setAttr($name, $value, $data = [])
    {
        if (is_null($value) && $this->autoWriteTimestamp && in_array($name, [$this->createTime, $this->updateTime])) {
            // 自动写入的时间戳字段
            $value = $this->autoWriteTimestamp($name);
        } else {
            // 对文件处理 排序处理
            if (is_array($value) && array_key_exists($name, $this->file)) {
                ksort($value);
            }

            // 检测修改器
            $method = 'set' . Loader::parseName($name, 1) . 'Attr';
            if (method_exists($this, $method)) {
                $value = $this->$method($value, array_merge($this->data, $data), $this->relation);
            } elseif (isset($this->type[$name])) {
                // 类型转换
                $value = $this->writeTransform($value, $this->type[$name]);
            }
        }

        // 设置数据对象属性
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getAttr($name)
    {
        try {
            $notFound = false;
            $value    = $this->getData($name);
        } catch (InvalidArgumentException $e) {
            $notFound = true;
            $value    = null;
        }

        if (in_array($name, $this->relation_alias)) {
            $name = array_search($name, $this->relation_alias);
        }

        // 检测属性获取器
        $method = 'get' . Loader::parseName($name, 1) . 'Attr';
        if (method_exists($this, $method)) {
            $value = $this->$method($value, $this->data, $this->relation);
        } elseif (isset($this->type[$name])) {
            // 类型转换
            $value = $this->readTransform($value, $this->type[$name]);
        } elseif (in_array($name, [$this->createTime, $this->updateTime])) {
            if (is_string($this->autoWriteTimestamp) &&
                in_array(strtolower($this->autoWriteTimestamp), ['datetime', 'date', 'timestamp'])) {
                $value = $this->formatDateTime(strtotime($value), $this->dateFormat);
            } else {
                $value = $this->formatDateTime($value, $this->dateFormat);
            }
        } elseif ($notFound) {
            // 不存在该字段 获取关联数据
            $relation = Loader::parseName($name, 1, false);
            if (method_exists($this, $relation)) {
                $value = $this->getRelation($relation);
            } else {
                throw new InvalidArgumentException('property not exists:' . $this->class . '->' . $name);
            }
        }
        return $value;
    }

    /**
     * 预载入关联查询 返回模型对象
     * @access public
     * @param Model        $result   数据对象
     * @param string|array $relation 关联名
     * @return void
     */
    public function eagerlyResult(&$result, $relation)
    {
        parent::eagerlyResult($result, $relation);
    }

    /**
     * 获取当前模型的关联模型数据
     * @access public
     * @param string $name 关联方法名
     * @return static|array
     */
    public function getRelation($name = null)
    {
        if (is_null($name)) {
            return $this->relation;
        }
        $relation = $name;
        if (isset($this->relation_alias[$name])) {
            $name = $this->relation_alias[$name];
        }
        if (!array_key_exists($name, $this->relation)) {
            $modelRelation = $this->$relation();
            $this->setRelation($relation, $this->getRelationData($modelRelation));
        }
        return $this->relation[$name];
    }

    /**
     * 设置关联数据对象值
     * @access public
     * @param string $name  属性名
     * @param mixed  $value 属性值
     * @return $this
     */
    public function setRelation($name, $value)
    {
        // 检测修改器
        $method = 'set' . Loader::parseName($name, 1) . 'Relation';
        if (method_exists($this, $method)) {
            $value = $this->$method($value);
        }
        if (isset($this->relation_alias[$name])) {
            $name = $this->relation_alias[$name];
        }
        $this->relation[$name] = $value;
        return $this;
    }

    /**
     * 获取更新条件
     * @access protected
     * @return mixed
     */
    protected function getWhere()
    {
        // 删除条件
        $pk = $this->getPk();

        if (is_string($pk) && isset($this->data[$pk])) {
            $where = [$pk => $this->data[$pk]];
        } elseif (!empty($this->updateWhere)) {
            $where = $this->updateWhere;
        } else {
            $where = null;
        }

        // 增加多主键的更新条件
        if (is_null($where) && is_array($pk)) {
            $where = [];
            foreach ($pk as $v) {
                if (!isset($this->data[$v])) {
                    $where = null;
                    break;
                }
                $where[$v] = $this->data[$v];
            }
        }

        return $where;
    }

    /**
     * 数据写入 类型转换
     * @access public
     * @param mixed        $value 值
     * @param string|array $type  要转换的类型
     * @return mixed
     */
    protected function writeTransform($value, $type)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($type)) {
            list($type, $param) = $type;
        } elseif (strpos($type, ':')) {
            list($type, $param) = explode(':', $type, 2);
        }

        switch ($type) {
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
                if (empty($param)) {
                    $value = (float) $value;
                } else {
                    $value = (float) number_format($value, $param, '.', '');
                }
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'timestamp':
                if (!is_numeric($value)) {
                    $value = strtotime($value);
                }
                break;
            case 'datetime':
                $format = !empty($param) ? $param : $this->dateFormat;
                $value  = is_numeric($value) ? $value : strtotime($value);
                $value  = $this->formatDateTime($value, $format);
                break;
            case 'object':
                if (is_object($value)) {
                    $value = json_encode($value, JSON_FORCE_OBJECT);
                }
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'array':
                $value = (array) $value;
            // no break;
            case 'json':
                $option = !empty($param) ? (int) $param : JSON_UNESCAPED_UNICODE;
                $value  = json_encode($value, $option);
                break;
            case 'serialize':
                $value = serialize($value);
                break;
            // 新增
            case 'base64':
                $value = base64_encode($value);
                break;
            // 新增
            case 'plode':
                $param = empty($param) ? ',' : $param;
                $value = empty($value) ? '' : implode($param, $value);
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * 数据读取 类型转换
     * @access public
     * @param mixed        $value 值
     * @param string|array $type  要转换的类型
     * @return mixed
     */
    protected function readTransform($value, $type)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_array($type)) {
            list($type, $param) = $type;
        } elseif (strpos($type, ':')) {
            list($type, $param) = explode(':', $type, 2);
        }
        switch ($type) {
            case 'integer':
                $value = (int) $value;
                break;
            case 'float':
                if (empty($param)) {
                    $value = (float) $value;
                } else {
                    $value = (float) number_format($value, $param, '.', '');
                }
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'timestamp':
                if (!is_null($value)) {
                    $format = !empty($param) ? $param : $this->dateFormat;
                    $value  = $this->formatDateTime($value, $format);
                }
                break;
            case 'datetime':
                if (!is_null($value)) {
                    $format = !empty($param) ? $param : $this->dateFormat;
                    $value  = $this->formatDateTime(strtotime($value), $format);
                }
                break;
            case 'json':
                $value = json_decode($value, true);
                break;
            case 'array':
                $value = empty($value) ? [] : json_decode($value, true);
                break;
            case 'object':
                $value = empty($value) ? new stdClass() : json_decode($value);
                break;
            case 'serialize':
                try {
                    $value = unserialize($value);
                } catch (Exception $e) {
                    $value = null;
                }
                break;
            // 新增
            case 'base64':
                $value = base64_decode($value);
                break;
            // 新增
            case 'plode':
                $param = empty($param) ? ',' : $param;
                $value = empty($value) ? [] : explode($param, $value);
                break;
            default:
                if (false !== strpos($type, '\\')) {
                    // 对象类型
                    $value = new $type($value);
                }
        }
        return $value;
    }
}
