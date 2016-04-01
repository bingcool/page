<?PHP
//数组分页
public function ArrayPage(){
	// 查询数据
	$user = M('user')->field('id,username')->select();
	// 实例化对象
	$page = new \Org\ArrayPage(2,$user);
	$data = $page->show();
	$currentdata = $data['currentdata'];
	dump($currentdata);
	$pagehtml = $data['html'];
	$this->assign('currentdata',$currentdata);
	$this->assign('page',$pagehtml);
	$this->display();

}

// mysql查询分页
public function Page() {
	// 查询总数
	$count = M('user')->where($map)->count('id');
	$page = new \Org\Page($count,'',20);
	$limit = $page->setlimit();
	// 查找当前页数数据
	$currentdata = M('user')->field('id,username')->where($map)->limit($limit)->select();

	$this->assign('currentdata',$currentdata);
	$this->assign('page',$pagehtml);
	$this->display();
}
?>