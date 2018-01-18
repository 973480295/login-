<?php
/**
 * Created by PhpStorm.
 * User: 李祥
 * Date: 2017/12/12
 * Time: 16:20
 */
namespace app\controllers;
use Codeception\Module\Redis;
use Yii;
use app\models\Member_group;
use app\models\News;
use app\models\Users;
use yii\web\Controller;
use app\models\Member;
use app\models\Member_detail;
use app\models\Category_priv;
use yii\helpers\Json;

class RedisController extends Controller
{
    public $enableCsrfValidation=false;
    public function actionIndex(){
        $userlist=Users::find()->all(); //查询数据库
        $redis=Yii::$app->redis;
       //存入redis
        foreach ($userlist as $key=>$v){
            $redis->incr('userid');
            $redis->hmset('user:'.$key,
                                'id',$v['id'],
                                'username',$v['username'],
                                'sex',$v['sex'],
                                'idcate',$v['idcate'],
                                'dorm_id',$v['dorm_id'],
                                'iclass',$v['iclass'],
                                'adress',$v['adress'],
                                'nation',$v['nation'],
                                'major',$v['major'],
                                'birthday',$v['birthday'],
                                'famname',$v['famname'],
                                'stutel',$v['stutel'],
                                'famtel',$v['famtel']
                );
            $redis->rpush('uid',$key);
        }
//        echo "<pre/>";
//        print_r($userlist);
        //测试
//        $redis->set('username','ergou');
//       echo $redis->get('username');
    }

    //列表
    public function actionRedis(){
        $this->layout=false;
        $redis=Yii::$app->redis;
        $pagesize=8;
        $count=$redis->get('userid');//获取总条数值
        $page=Yii::$app->request->get('page')?Yii::$app->request->get('page'):1;//获取页数值 如果没有默认1
        $ids=$redis->lrange('uid',$pagesize*($page-1),$pagesize*$page-1);//获取区间的数据
        //遍历区间数据
        foreach ($ids as $info){
            $ulist[]=$redis->hgetall('user:'.$info);
        }
//        echo '<pre/>';
//        print_r($ulist);exit;
        return $this->render('redis',['ulist'=>$ulist,'page'=>$page]);//映射页面

    }
    //删除
    public function actionDeluser(){
        $id=Yii::$app->request->get('id');
//        echo $id;exit;
        $redis=Yii::$app->redis;
        $redis->del('user:'.$id);
        $redis->lrem('uid',1,$id);
        $redis->decr('userid');
    }
    //添加
    public function actionAdduser(){
        $this->layout=false;
        return $this->render('adduser');
    }
    public function actionDo_add(){
        $this->layout=false;
        $redis=Yii::$app->redis;
        $data=Yii::$app->request->post();
//        echo "<pre/>";
//        print_r($data);
        $counts=$redis->get('userid')+1;
//        echo $counts;
        $info=$redis->hmset('user:'.$counts,
            'id',$counts,
            'username',$data['username'],
            'sex',$data['sex'],
            'idcate',$data['idcate'],
            'dorm_id',$data['dorm_id'],
            'iclass',$data['iclass'],
            'adress',$data['adress'],
            'nation',$data['nation'],
            'major',$data['major'],
            'birthday',$data['birthday'],
            'famname',$data['famname'],
            'stutel',$data['stutel'],
            'famtel',$data['famtel']
        );
        $redis->incr('userid');
        $redis->rpush('uid',$counts);
        if($info){
            echo 'succ';
        }else{
            echo "fail";
        }
        return $this->render('adduser');

    }
    //修改
    public function actionUpuser(){
        $this->layout=false;
        $id=Yii::$app->request->get('id');
        echo $id;
        $redis=Yii::$app->redis;
        $info=$redis->hgetall('user:'.$id);
        return $this->render('upuser',['id'=>$id,'info'=>$info]);
    }
    public function actionDo_up(){
        $this->layout=false;
        $redis=Yii::$app->redis;
        $data=Yii::$app->request->post();
//        echo "<pre/>";
//        print_r($data);exit;
        $info=$redis->hmset('user:'.$data['id'],
            'id',$data['id'],
            'username',$data['username'],
            'sex',$data['sex'],
            'idcate',$data['idcate'],
            'dorm_id',$data['dorm_id'],
            'iclass',$data['iclass'],
            'adress',$data['adress'],
            'nation',$data['nation'],
            'major',$data['major'],
            'birthday',$data['birthday'],
            'famname',$data['famname'],
            'stutel',$data['stutel'],
            'famtel',$data['famtel']
        );
        if($info){
            echo 'succ';
        }else{
            echo "fail";
        }
    }
}