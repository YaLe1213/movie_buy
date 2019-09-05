<?php 
/**
 * 定义Model 类
 */
class Model
{
	public $pdo;
	public $redis;
	public $key;

	public function __construct()
	{
		// 实例化redis
		$this->redis = new Redis();
		// 链接redis
		$this->redis->connect("localhost",6379);
		$this->redis->auth("123456"); //密码
	}
	// 方法 获取每个 模块 的数据
	public function get($sql)
	{
		// 判断
		$this->key=md5($sql);
		// 获取缓存服务器的数据   并转为数组  去掉true 是对象型
		$data=json_decode($this->redis->get($this->key),true);
		// 判断
		if (empty($data)) {
			// 链接数据库
			$this->pdo=new PDO("mysql:host=localhost;dbname=movie","root","");
			// 设置字符集
			$this->pdo->exec("set names utf8");
			// 执行sql 语句
			$list=$this->pdo->query($sql);
			// 获取结果
			$data=$list->fetchAll(PDO::FETCH_ASSOC);
			// 给 redis 缓存服务器一份     json_encode转为字符串
			$this->redis->set($this->key,json_encode($data)); 
		}
		return $data;
	}
	
}





 ?>