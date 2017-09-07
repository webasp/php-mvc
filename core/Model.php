<?php
namespace core;


class Model
{
    protected $table;
    protected $tables;
    protected $dbHandle;
    protected $result;
    private $filter = '';
    private $field = '*';
    private $join = '';
    // 连接数据库
    public function __construct()
    {
        try {
            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8",Config::get('DB_HOST'),Config::get('DB_NAME'));
            $option = array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
            $this->dbHandle = new \PDO($dsn, Config::get('DB_USER'), Config::get('DB_PWD'), $option);
        } catch (\PDOException $e) {
            exit('错误: ' . $e->getMessage());
        }

        // 获取数据库表名
        if (!$this->table) {
            // 数据库表名与类名一致
            $this->table = strtolower(get_class($this));
        }
        $this->table = Config::get('DB_PREFIX').$this->table;
        $this->tables = Config::get('DB_PREFIX').$this->table;

    }

    // 还原查询条件
    public function destroy()
    {
        $this->join = null;
        $this->field = '*';
        $this->filter = null;
        $this->table = $this->tables;
    }

    // 选择数据库
    public function table($table){
        $this->table = Config::get('DB_PREFIX').$table;
        return $this;
    }

    // 查询条件
    public function where($where = [],$op ='AND')
    {
        $this->filter = ' WHERE ';
        $i = 1;
        $count = count($where);
        foreach ($where as $key=>$value){
            $this->filter .= ' '.$key.' ';
            if(is_array($value)){
                if(key($value) === 'IN'){
                    $this->filter .= key($value);
                    $this->filter .= '('.current($value).')';
                }else{
                    $this->filter .= key($value);
                    $this->filter .= '\''.current($value).'\'';
                }
            }else{
                $this->filter .= '=';
                $this->filter .= '\''.$value.'\'';
            }
            if($count != $i){
                $this->filter .= $op;
            }
            $i++;
        }
        return $this;
    }

    // join 关联
    public function join($table,$on = [],$link = 'INNER')
    {
        $this->join .= ' ';
        $this->join .= $link.' JOIN '.Config::get('DB_PREFIX').$table;
        if(is_array($on)){
            $this->join .= ' ON ';
            $this->join .= implode(' ', $on);
        }
        return $this;
    }
    // 选择表字段
    public function field($field = '*')
    {
        $this->field = $field;
        return $this;
    }
    // 排序条件
    public function order($order = '')
    {
        if(isset($order)) {
            $this->filter .= ' ORDER BY ';
            $this->filter .= $order;
        }
        return $this;
    }

    // 分页
    public function limit($limit){
        if(isset($limit)) {
            $this->filter .= ' LIMIT ';
            $this->filter .= $limit;
        }
        return $this;
    }

    // 带分页查询
    public function paginate($rows = '15')
    {
        if(!empty($_GET['page']) && isset($_GET['page']) && is_int($_GET['page'] + 0) && intval($_GET['page']) > 1){
            $listRows = (intval($_GET['page']));
        }else{
            $listRows = 1;
        }





        $total = $this->getCount();
        $totalRows = ceil($total/$rows);

        if($listRows > $totalRows){
            $listRows = $totalRows;
        }

        if($total > $rows){
            $res = new Page($total,$rows,$listRows);
            $page = $res->render();
            $this->filter .= $res->limit;
        }else{
            $page = '';
        }

        $sql = sprintf("SELECT %s FROM `%s` %s %s", $this->field, $this->table,$this->join,$this->filter);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        $this->destroy();
        $result =  $sth->fetchAll();
        $data['data'] = $result;
        $data['page'] = $page;
        return $data;
    }

    // 查询
    public function select()
    {
        $sql = sprintf("SELECT %s FROM `%s` %s %s", $this->field, $this->table,$this->join,$this->filter);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        $this->destroy();
        return $sth->fetchAll();
    }


    // 查询一条数据
    public function find()
    {
        $sql = sprintf("SELECT %s FROM `%s` %s %s", $this->field, $this->table,$this->join,$this->filter);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        $this->destroy();
        return $sth->fetch();
    }
    // 根据条件 (id) 删除
    public function del($id = null)
    {
        if($id){
            $sql = sprintf("DELETE FROM `%s` WHERE `id` IN('%s')", $this->table,$id);
        }else{
            $sql = sprintf("DELETE FROM `%s` %s", $this->table,$this->filter);
        }
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        $this->destroy();
        return $sth->rowCount();
    }
    // 自定义SQL查询，返回影响的行数
    public function query($sql)
    {
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        return $sth->rowCount();
    }
    // 新增数据
    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));
        return $this->query($sql);
    }

    // 同时新增多条数据
    public function addAll($data)
    {
        $sql = '';
        foreach ($data as $value){
            $sql .= sprintf("INSERT INTO `%s` %s;", $this->table, $this->formatInsert($value));
        }
        return $this->query($sql);
    }

    // 修改数据
    public function update($id, $data)
    {
        $sql = sprintf("UPDATE `%s` SET %s WHERE `id` = '%s'", $this->table, $this->formatUpdate($data), $id);
        return $this->query($sql);
    }
    // 将数组转换成插入格式的sql语句
    private function formatInsert($data)
    {
        $fields = array();
        $values = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $values[] = sprintf("'%s'", \zy($value));
        }
        $field = implode(',', $fields);
        $value = implode(',', $values);
        return sprintf("(%s) values (%s)", $field, $value);
    }
    // 将数组转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = '%s'", $key, \zy($value));
        }
        return implode(',', $fields);
    }

    // 查询总条数用于分页
    private function getCount()
    {
        $sql = sprintf("SELECT * FROM `%s` %s %s",$this->table,$this->join,$this->filter);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        return $sth->rowCount();



    }


}