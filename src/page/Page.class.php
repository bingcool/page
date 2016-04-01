<?php
namespace Org;
/**
*  分页类
* 实例化： $page = new \Org\Page($count [,$p,$per])
*          $count:查询结果总数，该参数必须在实例化时传入
*          $p:当前请求的是第几页，如果不指定该参数，将自动获取 get请求中的 'p' 参数。若'p'不存在或不合法，默认为 1
*          $per:每页显示多少条记录， 如果不指定该参数,将自动获取get请求中的 ’per' 参数。若'per'不存在或不合法，默认为 15
* 可选：获取限制条件，直接连接在SQL语句后面  $sql = "SELECT * FROM table1 ". $page->limit();
*可选: 设置需要在链接中携带的参数  $page->setParams(array("name1"=>$value1,"name2"=> $value));
* 最后调用 $page->show();  返回值包含html代码，可直接输出到页面中
* @author 黄增冰
*/
class Page {
        
        /**
        * 查询结果总数，必须指定
        */
        private $count; 

        /**
        * 查询结果总页数，计算得出
        */
        private $numOfPage;

        /**
        * 每页显示记录条数，有默认值，允许为空
        */
        private $per;

        /**
        *   请求的当前页，不指定时为默认1
        */
        private $page;

        /**
        * 
        */
        private $begin;

        /**
        * 开始页 计算得出
        */
        private $start;

        /**
        * 结束页 计算得出
        */
        private $end;

        /*
        * 需要携带的其他数据 &name=value形式
        */
        private $params;

        private $itemCount=10;
        /**
        * 构造函数,初始化
        */
        public function __construct($count,$page=0,$per=0) {
            $this->count = $count;
            //自动获取 get请求中的 'p' 参数。若'p'不存在或不合法，默认为 1
            $this->page = $page > 0 ? $page : (isset($_GET['p'])  && intval($_GET['p'] >0) ? intval($_GET['p']) : 1);
            //将自动获取get请求中的 ’per' 参数。若'per'不存在或不合法，默认为 15
            $this->per = $per >0 ? $per : (isset($_GET['per'])  && intval($_GET['per'] >0 ) ? intval($_GET['per']) : 15);
            $this->numOfPage = intval($this->count / $this->per);
            ($this->count % $this->per ) AND $this->numOfPage++;
            $this->page > $this->numOfPage AND $this->page = 1;
            $this->begin = ($this->page - 1) * $this->per;      //记录起始行数
            $this->start = $this->page - ($this->page % $this->itemCount) +($this->page % $this->itemCount == 0 ? 0 : 1);   //开始页
            $this->end = ($this->start + $this->itemCount) > $this->numOfPage ? $this->numOfPage : ($this->start + $this->itemCount); //结束页

        }


        /**
        * 输出limit条件 如 limit(0,1)
        * @return string  查询的范围，可直接连接到SQL语句最后面
        */
        public function limit() {
                return  ' limit '.$this->begin.','.$this->per;
        }

        public function setlimit(){
            return $this->begin.','.$this->per;
        }

        /**
        * 设置同时需要携带的URL参数，已数组 key=>value的方式传入   url?key=xxx  array("key"=>"xxx","tyty"=>"dsd")
        * @param $params 包含需要携带参数的数组
        */
        public function setParams($params) {
            foreach ($params as $key => $value) {
                $this->params .= '&'.$key. '='.$value;
            }
        }
        /**
        * 输出结果
        */
        public function show() {
            $html = "<ul class=\"pagination\" style=\"display:block;\">";
            if ($this->page > 1) {
                $html .= "<li><a href=\"?p=1{$this->params}\">首页</a></li>";
                $html .="<li class=\"previous\"><a href=\"?p=".($this->page-1)."{$this->params}\">上一页</a></li>";
             }
             for ($i = $this->start; $i <= $this->end; $i++) {
                if($i == $this->page) {
                    $html .= "<li class=\"active\"><a>{$i}</a></li>";
                    continue;
                }
                $html .= "<li><a href=\"?p={$i}{$this->params}\">{$i}</a></li>";
            }
            if($this->page < $this->numOfPage) {
                $html .= "<li class=\"naxt\"><a href=\"?p=".($this->page+1)."{$this->params}\">下一页</a></li>";
                $html .="<li><a href=\"?p={$this->numOfPage}{$this->params}\">末页</a></li>";
            }
            $html .= "<li class=\"disabled\"><a>共{$this->numOfPage}页{$this->count}条记录 </a></li>";
            $this->numOfPage = $this->numOfPage == 0 ? "1" : $this->numOfPage;
            $html .= "<li><input type=\"number\" min=\"1\" max=\"{$this->numOfPage}\" name=\"p\" style=\"width:80px;margin:0 3px 0 3px;display:inline-block;\" class=\"form-control\" value=\"{$this->page}\"></li>";
            $html .= "<li><input type=\"submit\" class=\"btn btn-primary\"value=\"跳转\"></li>";
            $html .= "</ul>";
            return $html;
        }
}
