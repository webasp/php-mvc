# 简单PHP MVC 框架

>  简单的PHP框架   MVC框架入门实例  仅用于学习理解MVC结构


## 目录结构

~~~
WEB部署目录
├─app                   应用目录
│  ├─Controller         控制器层
│  ├─Model              模型层
│  ├─view               视图层
│  ├─cache              缓存文件
│  │  ├─html            生成静态页面
├─config                配置目录
│  ├─config.php         系统配置文件
│  ├─route.php          路由配置文件
├─core                  框架文件
│  ├─Config.php         解析配置文件
│  ├─Controller.php     控制器基类文件
│  ├─Core.php           框架入口文件
│  ├─Exception.php      异常错误抛出
│  ├─Function.php       系统函数库
│  ├─Model.php          模型基类文件
│  ├─Page.php           分页文件
│  ├─Parser.php         markdown 解析器(引入)
│  ├─Route.php          路由解析器(引入)
│  ├─Tpl.php            模板标签解析器
├─static                静态资源文件
├─index.php             入口文件
~~~

## 路由
路由配置文件config/route.php 
配置路由需要引入 \\Core\\Route;

>  Route::get('路由地址/[参数]','控制器名@方法');

~~~
 Route::get(); //添加一个接受Get请求的路由
 Route::post() //添加一个接受Post请求的路由
 Route::put() //添加一个接受Put请求的路由
 Route::delete() //添加一个接受delete请求的路由
 Route::options() //添加一个接受options请求的路由
 Route::head() //添加一个接受head请求的路由

 // 实例

 Route::get('/','IndexController@index');
// 访问 app下的IndexController下的index()方法
 Route::post('post','IndexController@post');
// POST 提交到 app下的IndexController下的post()方法

~~~

## 路由参数

 (:all)  所以字符可以为空
 (:any)  不能为空字段或数字
 (:num)  只能是数字
 
 ~~~
 实例：
  Route::get('search/(:all)','SearchController@index');
  Route::get('tag/(:any)','TagsController@index');
  Route::get('article/(:num)','ArticleController@show');
访问地址：
  http://php.com/search/要搜索的内容
  http://php.com/tag/表情名称
  http://php.com/article/1
控制器接受参数：
class ArticleController{
       public function article($id){
           echo $id;
       }
      
}
 ~~~
## 控制器
> app 文件下 Controller 下
> IndexController.php 命名


```php
namespace app\Controller;
设置命名空间
use core\Controller;
引入Controller 基类
class IndexController  extends Controller
{
      public function index() {
           echo  'Hello world';
     }
}
```
> 引入基类的目的是 使用基类中提供的方法
> 1. assign() 方法
> 2. display() 方法
> 3. json() 方法

**assign('名称',变量名称) **
> 向模板中注入数据


```
// 格式：
$this->assign('data',$article);
```

**display('视图文件名'[,变量名称(['page'=>1])[,teue|false]]) **
>  载入视图文件

```
// 格式：
   $this->display('index');
```
 **json() 方法**
> 转成json 格式输出


```
// 格式：
$this->json($data);
```

## 视图
> app 文件下 view下
> index.html  这里的 index.html 就是 display('index') 引入的文件


## 模板标签
> 这个模板写的比较糙  了解它的工作原理就好 
> 之后可以使用一些像Smarty之类的模板引擎


普通变量
```
{#art}   
{$art.title}
```

系统变量
> 配置文件位置 Config/config.php

```
{#$url}
```

if标签
```
{if $article}...{else}...{/if}
```

Foreach标签

```
{foreach $article(key,value)}
  {@@key}
  {@value.title}
     {foreach @value(key,value)}
       // 子循环
     {/foreach}
{/foreach}
```

include语句
```
{@include "header"}
{@include "footer"}
```

## 数据库模型

> 链式操作

```
$article = new Article();
```
> 需要在 app/Model/Article.php  创建模型文件

```
namespace app\Model;
use core\Model;
class Article extends Model
{
    // 指定数据表
    public $table = 'article';

}
```
基础Model 基类后才可以使用Model 基类提供的数据库操作方法

### table()
> 重新选择数据表
```
$this->table('article')
```

### where()
> 条件查询
where($array[,and|or])
```
实例
$map = ['id'=1];  // id = 1
$map = ['id'=>['>']=>6];  // id>6
$map = ['id'=>['in'=>'1,3,4,5,6']];  // id in (1,3,4,5,6)
$map = ['user'=>['like'=>'dengyun']];  // user like '%dengyun%'
where($map)；

$map1 = [['id'=>3],\['id'=>5]]
where($map1[,and]) // id=3 and id=5
where($map1[,or]) // id=3 or id=5
```

### join()
> 表连接 
```
默认等值连接
join('表2',['表2.外键'，'=','表1.主键'])
 左外连接
join('表2',['表2.外键'，'=','表1.主键'],'left')
右外连接
join('表2',['表2.外键'，'=','表1.主键'],'right')

```

### field()
> 字段
```
field('id,title,desc...')
field('表1.id,表1.title,表2.id...')
```


### order()
> 排序
```
order('id DESC')
order('id ASC')
```

### limit()
> 输出条数

```
limit('15')
```

### find()
> 查询条数据 必须使用到最后

```
 xxx->find()
```

### select()
> 查询多条数据 必须使用到最后

```
 xxx->select()
```

### paginate()
查询多条数据带分页 必须使用到最后

```
 xxx->paginate()

```


### del()
> 删除数据

```
where(['id'=>1])->del();  // del id=1
xxx->del('2');  // del id=2
```

### add()
> 添加一条数据

```
$data['name'] = 'denyun';
$data['age'] = '18';
add($data);
```

### addAll()
添加一组数据

```
$data = [
   [
     'name'=>'dneyun',
     'age'=>'18'
   ],
   [
     'name'=>'dneyun2',
     'age'=>'188'
   ]
];
addAll($data);
```

### update()
> 更新数据
update(ID,data)
```
$data['name'] = 'denyun';
$data['age'] = '18';
update('1',$data)
```

>  此项目附 Blog 前台部分实例代码 

