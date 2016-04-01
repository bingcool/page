<?php
namespace Org;
/**
*数组分类页类
*
*
*/
class ArrayPage {
	/**
	*查询的数组记录总数,必须指出
	*/
	public $count;

	/**
	*整个数组数据，必须指出
	*/

	public $data;

	/**
	*当前页的数组,计算得出
	*/
	public $currentdata;

	/**
	*排序查找，可以为空
	*/
	public $order;

	/**
    * 查询结果总页数，计算得出
    */
    private $numOfPage;

    /**
    * 每页显示记录条数，有默认值，允许为空
    */
    private $per;

    /**
    *请求的当前页，不指定时为默认1
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

    /**
    *在页面显示分页页数
    *eg:1,2,3,4,5,6,next:7,8,9,10,11,12,显示按钮页数数目
    */
    private $itemCount=5;

    /**
    * 构造函数,初始化
    */
    public function __construct($per,$data,$order='') {
    	// 记录总数
    	$this->count = (isset($_GET['count'])&&!empty($_GET['count'])) ? ($_GET['count']) : count($data);
    	// 附带总数参数，避免每次都重新计算
    	$this->params = '&count='.$this->count;
    	//记录数组
    	$this->data = $data;
    	// 默认每一页显示的数
    	$this->per = $per>0 ? $per : 15;
    	//自动获取 get请求中的 'p' 参数。若'p'不存在或不合法，默认为 1
    	$this->page = $page > 0 ? $page : (isset($_GET['p'])  && intval($_GET['p'] >0) ? intval($_GET['p']) : 1);
    	// 总有几页总数
    	$this->numOfPage = intval($this->count / $this->per);
    	//判断是否整除
    	($this->count % $this->per) && $this->numOfPage++;
    	//数据页数传递过来大于总的页数时，返回第一页
    	($this->page > $this->numOfPage) && $this->page = 1;
    	// 排序,键名不变,值相反，返回一个新的数组
    	$order && $this->data = array_reverse($data,true);
    	// 开始个数
    	$this->begin = ($this->page - 1) * $this->per;
    	//开始页
    	$this->start = $this->page - ($this->page % $this->itemCount) +($this->page % $this->itemCount == 0 ? 0 : 1);   
        //结束页
        $this->end = ($this->start + $this->itemCount) > $this->numOfPage ? $this->numOfPage : ($this->start + $this->itemCount);
        // 当前页数据
        $this->currentdata=array_slice($this->data,$this->begin,$this->per);
        // var_dump($this->data);
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
        return $result=array(
        		'html'=>$html,
        		'currentdata'=>$this->currentdata
        	);
    }

}
?>