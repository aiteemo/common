<?php
/**
 * 全局公共函数.
 * User: sdf_sky
 * Date: 15/12/24
 * Time: 下午7:14
 */

/*生成一个24位唯一的订单号码*/
function order_no(){
    return date('Ymd').substr(time(),-5).substr(microtime(),2,5).'00'.rand(1000,9999);
}

/*生成一个12位唯一的卡券号码*/
function card_code($head_str){
    $headAry = array(5,6,8,9);
    $head = $head_str ? $head_str : $headAry[rand(0,3)];
    return $head.substr(microtime(),2,5).rand(100,999).rand(100,999);
}

function send_sms_post($data,$target){
    $url_info = parse_url($target);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
    $httpheader .= $data;

    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
    while(!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);
    if($gets != ''){
        $start = strpos($gets, '<?xml');
        if($start > 0) {
            $gets = substr($gets, $start);
        }
    }
    return $gets;
}

function send_sms($mobile,$message,$from='链养鸡',$sprdid='1012818'){
    $target = "http://cf.51welink.com/submitdata/Service.asmx/g_Submit";
    //$post_data = "sname=dljiaruk&spwd=oc46WhbB&scorpid=&sprdid=".$sprdid."&sdst=".$mobile."&smsg=".rawurlencode($message."【".$from."】");
    $post_data = "sname=dljiaruk&spwd=dpark0405&scorpid=&sprdid=".$sprdid."&sdst=".$mobile."&smsg=".rawurlencode($message."【".$from."】");
    $gets = send_sms_post($post_data, $target);
    $result = explode($gets,'');
    if($result[0] == 0){
        return true;
    }else{
        return false;
    }

}

/**
 * 发送验证码，调用send_sms_message()方法完成短信验证码发送功能
 * @param  [string] $mobile [手机号]
 * @return [int]
 */
function send_sms_code($mobile,$from='链养鸡'){
    if(mb_strlen($mobile) != 11){
        return 2;   //手机格式不正确
    }

    $code = rand(100000,999999);
    $message = $code.'是您本次的短信验证码，5分钟内有效';
    $data['message'] = $message;
    $data['mobile'] = $mobile;
    $data['created_at'] = time();
    $smsModel = M('Sms');
    $smsData = $smsModel->where(array('mobile'=>array('eq',$mobile)))->order('created_at desc')->limit(1)->find();
    if($smsData){
        //判断距离上次发送时间
        if(time() - $smsData['time'] < 60){
            $this->api_error('','两次短信发送间隔不能小于1分钟');
        }
    }
    if(send_sms($mobile,$message,$from)){
        $result = M('sms')->add($data);
        if($result){
            $return_data['code'] = $code;
            $return_data['send_time'] = $data['created_at'];
            return $return_data;   //短信发送成功
        }else{
            return 3;   //数据库添加数据失败
        }
    }else{
        return 4;   //短信发送失败
    }
}


/*获取当前完整地址*/
function getPresentCompletaUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}


/*curl 发起请求*/
function curl_request($url,$data='',$header=0){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //curl_setopt($ch,CURLOPT_TIMEOUT,10);

    //post
    if(!empty($data)) {
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    //$header = array('Content-Type: application/json; charset=utf-8');
    curl_setopt($url, CURLOPT_HTTPHEADER,$header);
    //curl_setopt ($ch,CURLOPT_HEADER,0);
    $token_outopt = curl_exec($ch);
    curl_close($ch);
    return $token_outopt;
}

/**
 * 创建&提交FORM表单
 * @param string $url 需要提交到的地址
 * @param array $data 需要提交的数据
 * @return void
 * @author chunkuan <urcn@qq.com>
 */
function build_form($url, $data){
    $sHtml = "<!DOCTYPE html><html><head><title>Waiting...</title>";
    $sHtml.= "<meta http-equiv='content-type' content='text/html;charset=utf-8'></head>
	  <body><form id='lakalasubmit' enctype='application/json' name='lakalasubmit' action='".$url."' method='POST'>";
    foreach($data as $key => $value){
        $sHtml.= "<input type='hidden' name='".$key."' value='".$value."' style='width:90%;'/>";
    }
    $sHtml .= "</form>正在提交订单信息...";
    $sHtml .= "<script>document.forms['lakalasubmit'].submit();</script></body></html>";
    exit($sHtml);
}

/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
    $arg  = "";
    while (list ($key, $val) = each ($para)) {
        $arg.=$key."=".$val."&";
    }
    //去掉最后一个&字符
    $arg = substr($arg,0,count($arg)-2);

    //如果存在转义字符，那么去掉转义
    if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

    return $arg;
}

/*去除数组里面为空的字段*/
function removeEmptyKeys($data) {
    $res = array();
    if(!is_array($data)) {
        return $data;
    }
    while(list($k,$v)=each($data)) {
        if($v) {
            $res[$k] = $v;
        }
    }
    return $res;
}


/**
 * RSA签名
 * @param $data 待签名数据
 * @param $private_key_path 商户私钥文件路径
 * return 签名结果
 */
function rsaSign($data, $private_key_path) {
    $priKey = file_get_contents($private_key_path);
    $res = openssl_get_privatekey($priKey);
    openssl_sign($data, $sign, $res);
    openssl_free_key($res);
    //base64编码
    $sign = base64_encode($sign);
    return $sign;
}
/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
    ksort($para);
    reset($para);
    return $para;
}

//获取2点之间的距离
function GetDistance($lat1, $lng1, $lat2, $lng2)
{
    $radLat1 = $lng1 * (M_PI / 180);
    $radLat2 = $lng2 * (M_PI / 180);

    $a = $radLat1 - $radLat2;
    $b = ($lat1 * (M_PI / 180)) - ($lat2 * (M_PI / 180));

    $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    $s = $s * 6370996.81;
    $s = round($s * 10000) / 10000;
    return $s;
    //return 6370996.81 * acos(cos($lng1 * M_PI / 180) * cos($lng2 * M_PI / 180) * cos($lat1 * M_PI / 180 - $lat2 * M_PI / 180) + sin($lng1 * M_PI / 180) * sin($lng2 * M_PI / 180));
}

/*根据两地距离排序*/
function cmp($a, $b)
{
    return $a["distance"] > $b["distance"];
}

/*获取当前地址*/
function getSelf() {

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
    return $protocol.$_SERVER['SERVER_NAME'];
}

function check_not_null_param($not_null_param,$data)
{
    // 检查参数
    foreach ($not_null_param as $k=>$v)
    {
        if(!$data[$k]) return $v;
    }
}

/*获取钱包id,不存在则添加*/
function getEggcoinAccountId($eggcoin_account_address)
{
    $m   = M('EggcoinAccount');
    $res = $m->where('account_address="'.$eggcoin_account_address.'"')->getField('id');
    if(!$res) {
        $add['account_address'] = $eggcoin_account_address;
        $add['created']         = date('Y-m-d H:i:s');
        $add['state']           = 1;
        $res = $m->add($add);
    }
    return $res;
}

/*根据id获取钱包地址*/
function getEggcoinAccountInfoById($eggcoin_account_id)
{
    $m = M('EggcoinAccount');
    if($eggcoin_account_id)
    {
        return $m->where('id='.$eggcoin_account_id)->find();
    }
}

/*添加用户VIP信息*/
function addVip($user_id,$expiry_time)
{
    $m = M('UserVip');
    if($m->where('user_id='.$user_id)->find()) return true;
    $add['user_id'] = $user_id;
    $add['expiry_time'] = $expiry_time;
    $add['created'] = time();
    return $m->add($add);
}

/*记录饲养流水*/
function addRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id' => '用户不能为空',
        'amount' => '请填写数量',
        'unit' => '请填写单位',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }
    $raise_record_m = M('ChickenRaiseRecord');
    $raise_record = array();
    $raise_record['user_id'] = $data['user_id'];
    $raise_record['amount']  = $data['amount'];
    $raise_record['unit']    = $data['unit'];
    if($data['reason_source_id']) $raise_record['reason_source_id'] = $data['reason_source_id'];
    if($data['chicken_id']) $raise_record['chicken_id'] = $data['chicken_id'];
    $raise_record['reason_type']      = $data['reason_type'];//事由类型id：1=>'充值',2=>'认购',3=>'投喂',4=>'支出',5=>'收益',6=>'饲料补扣',7=>'支出补扣',8=>'赠送',9=>'奖励',
    $raise_record['reason_narration'] = $data['reason_narration'];
    $raise_record['created_at'] = time();
    $raise_record['state'] = $data['state'] ? $data['state'] : 1; //状态：1.成功;2.失败;3.待处理
    $res =  $raise_record_m->add($raise_record);
    if(!$res)
    {
        $return_data['msg'] = '添加失败';
    }
    else
    {
        $return_data['code']= 1;
        $return_data['msg'] = 'success';
    }
    return $return_data;
}

function eggcoinReasonType($type_id)
{
    $arr = array(
        1 => '母鸡产出',
        2 => '赠送',
        3 => '邀请好友',
        4 => '异常补发',
    );
    if(!$type_id or !isset($arr[$type_id])) return '1';

    return $arr[$type_id];
}

function issueEggCoin($amount,$account_address,$recordTransaction=1,$reason_source_id='',$reason_type='',$reason_narration='')
{
    $return_data = array('code'=>0,'msg'=>'');

    //return $return_data;

    if(!$amount or !$account_address)
    {
        $return_data['msg'] = '参数错误';
        return $return_data;
    }
    //$amount=1;
    //$account_address = '1KofcVX71Zpq5imj9Vw9hahFxu843NcTxY';
    //$account_address = '16xnbGyGqzDeD52gMjHunfa2psZdCJPvYW';
    // $a = urlencode(iconv("gb2312", "UTF-8", "电影"));
    $reason_narration = $reason_narration ? urlencode(iconv("gb2312", "UTF-8", $reason_narration )) : '1';
    $reason_type      = eggcoinReasonType($reason_type);
    $coin_type        = urlencode($reason_type);
    $url  = "http://api.lianyangji.io:8080/HelloSpringMVC/eggcoin/sendEggcoin?amount=".$amount."&owner_account=".$account_address."&coin_type=".$coin_type.'&chicken_no='.$reason_narration;
    if($reason_narration=='A110')
    {
       //echo $reason_type; echo $url;
    }
    /*
    $opts = array(
        'http'=>array(
            'method'=>"GET",
            'timeout'=>3,//单位秒
        )
    );*/

    //$json = file_get_contents($url,false,stream_context_create($opts));
    $json = file_get_contents($url);
    $arr  = json_decode($json,true);

    if($arr && $arr['retmsg']=='OK')
    {
        // 备份记录资产
        if($recordTransaction==1) recordTransaction($account_address,$amount,$type='IN');

        $return_data['code'] = 1;
        $return_data['msg']  = 'success';
    } else {
        $return_data['msg'] = $arr['retmsg'] ? $arr['retmsg'] : 'ERROR';
    }
    $return_data['info'] = $arr;
    addIssueEggCoinLog($amount,$account_address,$json,$reason_source_id,$reason_type);
    return $return_data;
}

/*发币日志*/
function addIssueEggCoinLog($amount,$account_address,$log,$reason_source_id='',$reason_type='')
{
    $m = M('Issue_log');
    $data['amount'] = $amount;
    $data['account_address'] = $account_address;
    $data['info'] = $log ? $log : '';
    $data['add_time'] = time();
    if($reason_type) $data['reason_type'] = $reason_type;
    if($reason_source_id) $data['reason_source_id'] = $reason_source_id;
    $m->add($data);
}

function getAssetAccountInfoByAddress($address)
{
    $url  = 'http://api.lianyangji.io:8080/HelloSpringMVC/eggcoin/getCoinNum?asset_account='.$address;
    $json = file_get_contents($url);
    if($json) $arr = json_decode($json,true);
    if($arr)  return $arr;
}

/*记录数字发行流水*/
function addEggcoinRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id' => '用户不能为空',
        //'eggcoin_account_id' => '钱包id不能为空',
        'amount' => '请填写数量',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }
    $raise_record_m = M('EggcoinRecord');
    $raise_record = array();
    $raise_record['user_id'] = $data['user_id'];
    if($data['eggcoin_account_id']) $raise_record['eggcoin_account_id'] = $data['eggcoin_account_id'];
    $raise_record['amount']  = $data['amount'];
    if($data['reason_source_id']) $raise_record['reason_source_id'] = $data['reason_source_id'];
    if($data['chicken_id']) $raise_record['chicken_id'] = $data['chicken_id'];
    if($data['err_code']) $raise_record['err_code'] = $data['err_code'];

    $raise_record['reason_type']      = $data['reason_type'];//事由类型id：1.收益；2.赠送；3.奖励'
    $raise_record['reason_narration'] = $data['reason_narration'];
    $raise_record['created_at'] = $raise_record['updated_at'] = time();
    $raise_record['state'] = $data['state'] ? $data['state'] : 1; //状态：1.成功;2.失败;3.待处理
    $res =  $raise_record_m->add($raise_record);
    if(!$res)
    {
        $return_data['msg'] = '添加失败';
    }
    else
    {
        $return_data['code']= 1;
        $return_data['msg'] = 'success';
        $return_data['data']['id'] = $res;
    }
    return $return_data;
}

/*递归创建目录*/
function directory( $dir )
{
    is_dir ( $dir )  or  (Directory(dirname( $dir ))  and   mkdir ( $dir , 0777));
}



// 根据手机号添加用户
function addUserByMobile($mobile)
{
    $re_data = array('code'=>1,'msg'=>'success');

    if(!$mobile)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '手机号不可为空';
        return $re_data;
    }

    // 查询手机是否存在
    $m    = M('User');
    $info = $m->where('mobile='.$mobile)->find();
    if(!$info)
    {
        $user_data['mobile']  = $mobile;
        $user_data['user_st'] = 1;
        $user_data['created_at'] = $user_data['updated_at'] = time();
        $re_data['data']['user_id'] = $user_id = $m->add($user_data);
        if(!$user_id)
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '注册失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '手机号码已存在';
    }

    // 新用户操作
    if($user_id) newUserAction($user_id);
    return $re_data;
}

// 根据微信号添加用户
function addUserByWechat($wechat_info)
{
    $re_data = array('code'=>1,'msg'=>'success');

    // 查询微信账号是否存在
    $m        = M('UserWechatinfo');
    //$w_info   = $m->where('wx_open_id="'.$wechat_info['wx_open_id'].'"')->find();
    $w_u_info = $m->where('unionid="'.$wechat_info['unionid'].'"')->find();

    if($w_u_info)
    {
        if($wechat_info['wx_nick_name'] && !testEmoji(base64_decode($wechat_info['wx_nick_name'])))
        {
            $wechat_info['nick_name'] = base64_decode($wechat_info['wx_nick_name']);
        }
        // 更新信息
        $m->where('unionid="'.$wechat_info['unionid'].'"')->save($wechat_info);

        $re_data['code'] = 20003;
        $re_data['msg']  = '该微信已被绑定';
        $re_data['data']['user_id']  = $w_u_info['user_id'];
        return $re_data;
    }

    $trans = M();
    $trans->startTrans();

    // 注册一个账号
    $user_data['user_st'] = 1;
    $user_data['created_at'] = $user_data['updated_at'] = time();
    $re_data['data']['user_id'] = $user_id = M('User')->add($user_data);
    if(!$user_id) {
        $re_data['code'] = 20002;
        $re_data['msg']  = '注册失败';
        return $re_data;
    }

    // 新用户操作
    if($user_id) newUserAction($user_id);

    // 绑定微信
    $res = bindWeChat($user_id,$wechat_info);
    if($res['code']!=1)
    {
        $trans->rollback();
        $re_data['code'] = 20002;
        $re_data['msg']  = '注册失败!'.$res['msg'];
    }
    $trans->commit();
    return $re_data;
}

// 新用户操作
function newUserAction($user_id)
{
    //echo $user_id;die;
    // 添加vip
    //if($user_id <= 10000) addVip($user_id,time()+85400*365*10);

    // 生成钱包
    $wallet_m = M('Wallet');
    $wallet_map['user_id']     = $user_id;
    $wallet_info               = $wallet_m->where($wallet_map)->find();
    if(!$wallet_info)
    {
        // 给初次用户赠送300g饲料
        $wallet_map['feed_amount'] = 0.3;
        $wallet_m->add($wallet_map);

        // 添加赠送饲料流水
        $record = array();
        $record['user_id'] = $user_id;
        $record['amount']  = $wallet_map['feed_amount']*1000;
        $record['reason_source_id'] = $user_id;
        $record['reason_type'] = 8;
        $record['reason_narration'] = 'VIP内测用户奖励';
        $record['state'] = 1;
        $record['unit'] = 'g';
        $record = addRecord($record);
        if(!$record) Log::record('用户赠送300g饲料流水记录失败,INFO:'.json_encode($record),'ADD_RECORD',true);
    }

    // 检查邀请码
    M('User')->where('invite_code = "" and id='.$user_id)->setField('invite_code',strtoupper(substr(md5($user_id.'teemo'),8,16)));
}

// 绑定手机
function bindMobile($user_id,$mobile)
{
    $re_data = array('code'=>1,'msg'=>'success');

    if(!$mobile)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '手机号不可为空';
        return $re_data;
    }

    // 查询手机是否存在
    $m    = M('User');
    $info = $m->where('mobile='.$mobile)->find();
    if(!$info)
    {
        if(!$m->where('id='.$user_id)->setField('mobile',$mobile))
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '绑定失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '手机号码已存在';
    }
    return $re_data;
}

// 绑定微信
function bindWeChat($user_id,$wechat_info)
{
    $re_data = array('code'=>1,'msg'=>'success');

    $arr = array(

    );
    $not_null_param = array(
        //'wx_open_id'   => '获取微信open_id失败',
        'unionid'   => '获取微信信息失败',
        //'wx_pic'       => '缺少微信用户头像',
        //'wx_nick_name' => '缺少微信用户昵称',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$wechat_info);
    if($check_res) {
        $re_data['code'] = 20001;
        $re_data['msg']  = $check_res;
        return $re_data;
    }

    // 查询微信账号是否存在
    $m        = M('UserWechatinfo');
    //$w_info   = $m->where('wx_open_id="'.$wechat_info['wx_open_id'].'"')->find();
    $w_u_info = $m->where('unionid="'.$wechat_info['unionid'].'"')->find();

    //if(!$w_info and !$w_u_info)
    if(!$w_u_info)
    {
        $wechat_info['user_id'] = $user_id;
        $wechat_info['created_at'] = time();
        if($wechat_info['wx_nick_name'] && !testEmoji(base64_decode($wechat_info['wx_nick_name'])))
        {
            $wechat_info['nick_name'] = base64_decode($wechat_info['wx_nick_name']);
        }
        $res = $m->add($wechat_info);
        if(!$res)
        {
            $re_data['code'] = 20002;
            $re_data['msg']  = '绑定失败';
        }
    }
    else
    {
        $re_data['code'] = 20003;
        $re_data['msg']  = '该微信已被绑定';
    }
    return $re_data;
}

// 根据用户id获取用户信息
function getUserInfoByUserId($user_id)
{
    $re_data = array('code'=>1,'msg'=>'success');
    $m = M('User');
    if(!$user_id)
    {
        $re_data['code'] = 20001;
        $re_data['msg']  = '缺少参数';
        return $re_data;
    }

    $map['user_id'] = $m_map['id'] = $user_id;

    $info = $m->where($m_map)->field('code,send_time,trade_pass_wd',true)->find();
    if(!$info)
    {
        $re_data['code'] = 20002;
        $re_data['msg']  = '用户不存在';
        return $re_data;
    }

    // 邮箱状态
    if(!$info['email']) $info['email_status'] = 3;
    $info['email_status_info'] = getEmailStatusInfo($info['email_status']);

    // 微信信息
    $wechart_info = M('UserWechatinfo')->field('user_id,created_at',true)->where($map)->find();
    $info['wechart_info'] = $wechart_info ? $wechart_info : array();
    if($info['wechart_info'])
    {
        $info['wechart_info']['wx_nick_name'] = base64_decode($info['wechart_info']['wx_nick_name']);
        $info['wechart_info']['sex_info'] = getWechatUserSexInfo($info['wechart_info']['sex']);
    }

    // vip
    $vip = M('UserVip')->where($map)->find();
    $info['vip'] = $vip ? 1 : 2;
    $info['vip_info'] = $info['vip']==1 ? '内测会员' : '普通会员';

    // 昵称
    if(!$info['full_name']) $info['full_name'] = $info['wechart_info']['wx_nick_name'];
    if(!$info['pic']) $info['pic'] = $info['wechart_info']['wx_pic'];

    // 注册时间
    $info['created_date'] = date('Y-m-d H:i:s',$info['created_at']);
    $info['updated_date'] = date('Y-m-d H:i:s',$info['updated_at']);

    // 微博
    $info['weibo_info'] = array();
    $re_data['data'] = $info;
    return $re_data;
}

/*微信性别*/
function getWechatUserSexInfo($id)
{
    if(!$id) return '未知';
    $info = array(
        1 => '🚹男士',
        2 => '🚺女士',
        3 => '未知',
    );
    return $info[$id];
}

/*邮箱状态*/
function getEmailStatusInfo($status)
{
    if(!$status) return '未设置邮箱';
    $status_info = array(
        1 => '已验证通过',
        2 => '待验证',
        3 => '未设置邮箱',
    );
    return $status_info[$status];
}

/*任务奖励*/
function task_reward($task='')
{
    $reward = array(
        'invite_reward' => 1,
        'sign_reward' => 10,
        'share_reward' => 20,
        'friend_login_reward' => 30,
        'transfer_success_reward' => 40
    );
    if(!$task) return $reward;
    return $reward[$task];
}

/*邀请购买成功奖励*/
function invite_success_reward($user_id,$invite_id='')
{
    // 数字发行
    $eggcoin_data = array();
    $eggcoin_data['user_id'] = $user_id;
    $eggcoin_data['amount'] = task_reward('invite_reward');
    $eggcoin_data['reason_type'] = 3;//事由类型id：1.收益；2.赠送；3.奖励'
    $eggcoin_data['reason_narration'] = '邀请购买';//事由名称
    if($invite_id) $eggcoin_data['reason_source_id'] = $invite_id;

    // 钱包地址
    $chicken_map = array();
    $chicken_map['state']   = 5;
    $chicken_map['user_id'] = $user_id;
    $chicken_info           = M('Chicken')->where($chicken_map)->order('created desc')->find();

    if($chicken_info) $eggcoin_account_id = $chicken_info['eggcoin_account_id'];

    if($eggcoin_account_id) $eggcoin_account = getEggcoinAccountInfoById($eggcoin_account_id);

    if(!$eggcoin_account_id or !$eggcoin_account or !$eggcoin_account['account_address'])
    {
        $eggcoin_data['state']    = 3;//状态：1.成功;2.失败;3.待处理'
        $eggcoin_data['err_code'] = 'ADDRESS_NULL';
    }
    else
    {
        // 发放币
        $issueEggCoin_res = issueEggCoin((int)$eggcoin_data['amount'], $eggcoin_account['account_address'],$recordTransaction=1,$eggcoin_data['reason_source_id'],$eggcoin_data['reason_type']);
        //issueEggCoin($amount,$account_address,$recordTransaction=1,$reason_source_id='',$reason_type='',$reason_narration='')
        //send_sms(18510249173,json_encode($issueEggCoin_res));
        if( $issueEggCoin_res['code'] == 1 )
        {
            $eggcoin_data['state'] = 1;//状态：1.成功;2.失败;3.待处理'
        }
        else
        {
            $eggcoin_data['state'] = 3;//状态：1.成功;2.失败;3.待处理'
            $eggcoin_data['err_code'] = 'ISSUE_ERROR';
        }
        $chicken_info['chicken_id']   = $chicken_info['id'];
        $eggcoin_data['eggcoin_account_id'] = $eggcoin_account_id;
    }

    $eggcoin_record = addEggcoinRecord($eggcoin_data);
    if($eggcoin_record['code']==0)
    {
        //Log::record('数字发行记录失败,INFO:' . json_encode($eggcoin_data), 'invite_success_reward', true);
    }
    return $eggcoin_record;
}

/*分享成功奖励*/


/*邀请好友登录成功奖励*/

/*
     *  获取当前发行批次鸡
     * */
function getCurrentBatch($type=1)
{
    $m = M('ChickenBatch');
    $map = array();
    $map['state'] = 1;
    $map['is_default'] = 1;

    if($type==2)
    {
        $map = array();
        $map['start_time'] = array('lt',time());
        $map['end_time']   = array('gt',time());
        $map['id'] = 2;
    }
    // 发行时间
    //$map['start_time'] = array('lt',time());
    //$map['end_time']   = array('gt',time());
    return $m->where($map)->find();
}


//3.14号补发
function reissue()
{
    return;
    $c_m   = M('Chicken');
    $e_r_m = M('EggcoinRecord');
    $map = array();
    //echo "<pre>";
    // 找出3点半之前发放的币;
    $recordList = $e_r_m->where("created_at <= 1521012480")->select();
    //$recordList = $e_r_m->where("chicken_id is null")->select();
    // REISSUE reissue
    foreach ($recordList as $rk=>$rv)
    {
        // 钱包地址
        $chicken_map = array();
        $chicken_map['state']   = array('in',array(4,5));
        $chicken_map['id']      = $rv['chicken_id'];
        $chicken_map['user_id'] = $rv['user_id'];
        $chicken_info = $c_m->where($chicken_map)->find();
        if($chicken_info && $chicken_info['eggcoin_account_id'] && ($chicken_info['eggcoin_account_id']!=$rv['eggcoin_account_id']) && ($rv['err_code'] != 'REISSUE_SUCCESS'))
        {
            // 钱包地址
            $eggcoin_account = getEggcoinAccountInfoById($chicken_info['eggcoin_account_id']);
            if($eggcoin_account and $eggcoin_account['account_address'])
            {
                $issueEggCoin_res = issueEggCoin((int)$rv['amount'],$eggcoin_account['account_address']);
                $update_data = array();
                $update_data['state']    = 1;//状态：1.成功;2.失败;3.待处理'
                if($issueEggCoin_res['code']==1)
                {

                    $update_data['err_code'] = 'REISSUE_SUCCESS';
                }
                else
                {
                    $update_data['err_code'] = 'REISSUE_ERROR';
                }

                // 如果补发成功把发放地址修改回来
                if($update_data['err_code']=='REISSUE_SUCCESS') $update_data['eggcoin_account_id'] = $chicken_info['eggcoin_account_id'];
                $res = $e_r_m->where('id='.$rv['id'])->save($update_data);
                //echo $res,'<br>';
            }
        }
        //if($chicken_info) $e_r_m->where('id='.$rv['id'])->setField('chicken_id',$chicken_info['id']);
    }
}

// 提现状态
function withdrawls_state($state)
{
    $state_info = array(
        1=>'申请中',
        2=>'已同意',
        3=>'已完成',
        4=>'已拒绝',
        5=>'已撤销'
    );
    if(isset($state_info[$state])) return $state_info[$state];
}

//提现放款状态
function withdrawls_pay_state($pay_state)
{
    $pay_state_info = array(
        1=>'待确认',
        2=>'待放款',
        3=>'放款中',
        4=>'已完成',
        5=>'放款失败'
    );
    if(isset($pay_state_info[$pay_state])) return $pay_state_info[$pay_state];
}

//结算状态
function chicken_delivery_state($state)
{
    $state_info = array(
        1=>'待确认',
        2=>'待收取',
        3=>'成功',
        4=>'失败',
    );
    if(isset($state_info[$state])) return $state_info[$state];
}

//备份记录资产数量
function recordTransaction($account_address,$eggcoin_amount,$type)
{
    $type_info = array(
        'IN','OUT'
    );
    if(!$account_address or !$eggcoin_amount or ($eggcoin_amount < 1) or !$type or !in_array($type,$type_info))
    {
       return array('errcode'=>0,'msg'=>'参数错误');
    }

    $m = M('EggcoinAccount');
    $map = array();
    $map['account_address'] = $account_address;

    // 检测有没有地址
    $info = $m->where($map)->find();

    if(!$info)
    {
        $info['state']   = 1;
        $info['created'] = date('Y-m-d H:i:s');
        $info['eggcoin_amount']  = 0;
        $info['account_address'] = $account_address;
        $m->add($info);
    }

    if($type=='OUT')
    {
        // 检测余额
        if($info['eggcoin_amount'] < $eggcoin_amount)
        {
            return array('errcode'=>0,'msg'=>'余额不足');
        }

        $eggcoin_amount = -$eggcoin_amount;
    }

    if($m->where($map)->setInc('eggcoin_amount',$eggcoin_amount))
    {
        return array('errcode'=>1,'msg'=>'success');
    }
    return array('errcode'=>0,'msg'=>'失败!请稍后重试');
}

// 升级链补发
function bufaOldEggcoin($user_id)
{
    // 补发钱包地址
    $chicken_map = array();
    $chicken_map['state']   = 5;
    $chicken_map['user_id'] = $user_id;
    $chicken_info           = M('Chicken')->where($chicken_map)->order('updated desc')->find();

    if($chicken_info && $chicken_info['eggcoin_account_id'])
    {
        $eggcoin_account = getEggcoinAccountInfoById($chicken_info['eggcoin_account_id']);
    }

    // 没有地址则不补发
    if(!$eggcoin_account or !$eggcoin_account['account_address']) return;

    $m = M('BufaRecord');
    $map = array();
    $map['user_id'] = $user_id;
    $map['state']   = 4;
    $map['amount']  = array('gt',0);
    $list = $m->where($map)->field('id,eggcoin_account_address,amount')->select();

    foreach ($list as $v)
    {
        //echo $v['amount'],$eggcoin_account['account_address'];die;
        // 给新地址补发
        $res = issueEggCoin((int)$v['amount'],$eggcoin_account['account_address']);
        if($res['code']==1)
        {
            $save_map = $saveData = array();

            // 更新条件
            $save_map['id'] = $v['id'];
            $map['state']   = 4;

            // 更新数据
            $saveData['state'] = 1;
            $saveData['update_time'] = time();
            $saveData['bufa_eggcoin_account_address'] = $eggcoin_account['account_address'];
            $m->where($save_map)->save($saveData);
        }
        //print_r($res);
    }
}

// 获取app类型id
function getPlatformId($platform)
{
    switch ($platform)
    {
        case 'ios':
            $id = 2;
            break;
        case 'android':
            $id = 1;
            break;
        default:
            $id = 0;
    }
    return $id;
}

// 获取app类型
function getPlatform($platform_id)
{
    switch ($platform_id)
    {
        case 2:
            $platform = 'ios';
            break;
        case 1:
            $platform = 'Android';
            break;
        default:
            $platform = '未知';
    }
    return $platform;
}

// 地址为空的补发
function bufaAddressNull($user_id='',$limit='')
{
    $m = M('EggcoinRecord');
    $map = array();
    $map['state'] = 3;
    if($user_id) $map['user_id'] = $user_id;
    $map['err_code'] = 'ADDRESS_NULL';
    $list = $m->where($map)->select();
    if($limit) $list = $m->where($map)->limit($limit)->order('id desc')->select();
    if(!$list) return;

    $i = 0;
    foreach ($list as $k=>$v)
    {
        // 获取补发钱包地址,有鸡则查鸡绑定的钱包,没有则查最近绑定的钱包
        if($v['chicken_id'])
        {
            $chicken_info = M('Chicken')->where('id='.$v['chicken_id'])->find();
            $eggcoin_account_id = $chicken_info['eggcoin_account_id'];
        }
        else
        {
            // 补发钱包地址
            $chicken_map = array();
            $chicken_map['state']   = 5;
            $chicken_map['user_id'] = $v['user_id'];
            $chicken_info           = M('Chicken')->where($chicken_map)->order('updated desc')->find();

            if($chicken_info && $chicken_info['eggcoin_account_id'])
            {
                $eggcoin_account_id = $chicken_info['eggcoin_account_id'];
            }
        }
        // 没有地址id则不补发
        if(!$eggcoin_account_id) continue;

        $eggcoin_account = getEggcoinAccountInfoById($eggcoin_account_id);

        // 取不到地址则不补发
        if(!$eggcoin_account or !$eggcoin_account['account_address']) continue;

        // 补发资产
        $res = issueEggCoin((int)$v['amount'],$eggcoin_account['account_address'],1,$v['reason_source_id'],$v['reason_type'],$chicken_info['chicken_code']);
        //$issueEggCoin_res = issueEggCoin((int)$eggcoin_data['amount'], $eggcoin_account['account_address'],$recordTransaction=1,$eggcoin_data['reason_source_id'],$eggcoin_data['reason_type']);
        if($res['code']==1)
        {
            $save_map = $saveData = array();

            // 更新条件
            $save_map['id'] = $v['id'];

            // 更新数据
            $saveData['state'] = 1;
            $saveData['updated_at'] = time();
            $saveData['err_code']   = 'REISSUE_SUCCESS';
            $saveData['eggcoin_account_id'] = $eggcoin_account_id;
            $save_res = $m->where($save_map)->save($saveData);
            if($save_res) $i++;
        }
    }
    return $i;
}

// 失败的补发
function bufaIssueError($user_id='',$limit='')
{
    $m = M('EggcoinRecord');
    $map = array();
    $map['state'] = 3;
    if ($user_id) $map['user_id'] = $user_id;
    //$map['err_code'] = 'ISSUE_ERROR';
    $map['eggcoin_account_id'] = array('gt',0);
    $list = $m->where($map)->select();
    if ($limit) $list = $m->where($map)->limit($limit)->order('id desc')->select();
    if (!$list) return;

    $i = 0;
    foreach ($list as $k => $v) {
        // 没有地址id则不补发
        if (!$v['eggcoin_account_id']) continue;

        if($v['chicken_id']) $chicken_info = M('Chicken')->where('id='.$v['chicken_id'])->find();

        $eggcoin_account = getEggcoinAccountInfoById($v['eggcoin_account_id']);

        // 取不到地址则不补发
        if (!$eggcoin_account or !$eggcoin_account['account_address']) continue;

        // 补发资产
        //$res = issueEggCoin((int)$v['amount'], $eggcoin_account['account_address']);
        $res = issueEggCoin((int)$v['amount'],$eggcoin_account['account_address'],1,$v['reason_source_id'],$v['reason_type'],$chicken_info['chicken_code']);
        if ($res['code'] == 1) {
            $save_map = $saveData = array();

            // 更新条件
            $save_map['id'] = $v['id'];

            // 更新数据
            $saveData['state'] = 1;
            $saveData['updated_at'] = time();
            $saveData['err_code'] = 'REISSUE_SUCCESS';
            $saveData['eggcoin_account_id'] = $v['eggcoin_account_id'];
            $save_res = $m->where($save_map)->save($saveData);
            if ($save_res) $i++;
        }
    }
    return $i;
}

// 支付回调日志
function buyChickenNotifyLog($order_sn,$data,$come_from)
{
    // 2001 :h5微信; 1001 : app微信; 1002 : app支付宝
    if(!$order_sn or !$data or !$come_from) return;

    // 记录订单日志
    $wxpay_log_m = M('WxpayLog');
    $wxpay_log_res = $wxpay_log_m->where('order_sn="'.$order_sn.'"')->find();
    if($wxpay_log_res)
    {
        $wxpay_log_m->where('order_sn="'.$order_sn.'"')->setField('info',json_encode($data));
    }
    else
    {
        $wxpay_log_add = array();
        $wxpay_log_add['order_sn']  = $order_sn;
        $wxpay_log_add['info']      = json_encode($data);
        $wxpay_log_add['add_time']  = time();
        $wxpay_log_add['come_from'] = $come_from;
        $wxpay_log_m->add($wxpay_log_add);
    }
}

// 订单支付回调错误日志
function buyChickenNotifyErrorLog($order_sn,$data)
{
    if(!$order_sn or !$data) return;

    // 记录订单日志
    $m = M('OrderErrorLog');
    $log_add = array();
    $log_add['order_sn']  = $order_sn;
    $log_add['info']      = json_encode($data);
    $log_add['add_time']  = time();
    $m->add($log_add);
}

// 记录邀请码下单
function addInviteBuy($data)
{
    if(!$data['user_id'] or !$data['order_sn'] or !$data['invite_code']) return;

    //$count = M('Chicken')->where('(state=4 or state=5) and user_id='.$data['user_id'])->count();
    $invite_user_id = M('User')->where('invite_code="'.$data['invite_code'].'"')->getField('id');
    //if($invite_user_id and ($invite_user_id != $data['user_id']) and $count<1)
    if($invite_user_id and ($invite_user_id != $data['user_id']))
    {// 如果之前未购买,现在被邀请购买则记录

        $invite_m = M('InviteBuy');
        $invite_map['invite_user_id'] = $invite_user_id;
        $invite_map['user_id']        = $data['user_id'];
        $invite_info = $invite_m->where($invite_map)->order('add_time desc')->find();
        if(!$invite_info)
        {
            $invite_add['invite_user_id'] = $invite_user_id;
            $invite_add['user_id']   = $data['user_id'];
            $invite_add['add_date']  = date('Y-m-d');
            $invite_add['buy_state'] = 2;//状态：1.已购买；2.未购买
            $invite_add['buy_num']   = $data['num'] ? $data['num'] : 1;
            $invite_add['order_sn']  = $data['order_sn'];
            $invite_m->add($invite_map);
        }
        if($invite_info and $invite_info['buy_state']==2)
        {
            $invite_update['buy_num']   = $data['num'] ? $data['num'] : 1;
            $invite_update['order_sn']  = $data['order_sn'];
            $invite_m->where('id='.$invite_info['id'])->save($invite_update);
        }
    }
}

// 根据鸡获取赠送卡券
function getGiveCardListByChickenTypeId($chicken_type_id)
{
    if(!$chicken_type_id) return;
    $list = M('BuychickenGivecard')->field('card_id,num')->where('chicken_type_id='.$chicken_type_id)->select();
    return $list;
}

/*记录卡券下发流水*/
function addIssueCardRecord($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id' => '用户不能为空',
        'num' => '数量不可为空',
        'code_id_info' => '码ID',
        'reason_type' => '请填写流水类型',
        'reason_narration' => '请填写流水标题',
        'state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }
    $raise_record_m = M('CardIssue');
    $raise_record = array();
    $raise_record['user_id'] = $data['user_id'];
    $raise_record['num']     = $data['num'];
    $raise_record['code_id_info'] = json_encode($data['code_id_info']);
    $raise_record['reason_type'] = $data['reason_type']; // '事由类型id：1.下单赠送；2.系统赠送；3.任务奖励'
    $raise_record['reason_narration'] = $data['reason_narration'];
    $raise_record['create_time'] = time();
    $raise_record['state'] = $data['state']; // 状态：1.成功;2.失败;3.待处理

    if($data['reason_source_id']) $raise_record['reason_source_id'] = $data['reason_source_id'];
    if($data['unique_code']) $raise_record['unique_code'] = $data['unique_code'];
    if($data['err_code']) $raise_record['err_code'] = $data['err_code'];
    $res =  $raise_record_m->add($raise_record);

    if(!$res)
    {
        $return_data['msg'] = '添加失败';
    }
    else
    {
        $return_data['code']= 1;
        $return_data['msg'] = 'success';
    }
    return $return_data;
}

function takeYzCard($data)
{
    $return_data['code'] = 0;

    $not_null_param  = array(
        'user_id'   => '用户不能为空',
        'card_id'   => '卡ID不能为空',
        'num'       => '数量不可为空',
        'use_state' => '请填写状态',
    );

    // 检查参数
    $check_res = check_not_null_param($not_null_param,$data);
    if($check_res)
    {
        $return_data['msg']  = $check_res;
        return $return_data;
    }

    $m = M('CardCode');

    // 查询卡券状态
    $card_info = M('Card')->where('open_state=1 and id='.$data['card_id'])->find();
    if(!$card_info)
    {
        $return_data['msg']  = '卡券不存在或不可用';
        return $return_data;
    }

    $issue_datetime = time();

    // 绑定参数
    $saveData = $saveMap = array();
    $saveData['issue_datetime'] = $issue_datetime;
    $saveData['user_id'] = $data['user_id'];
    $saveData['use_state'] = $data['use_state'];
    $saveData['take_token'] = rand(100000,999999);
    if($card_info['received_days'])
    {
        $saveData['start_datetime'] = time();
        $saveData['end_datetime']   = $card_info['received_days']*86400+$saveData['start_datetime'];
    }
    else
    {
        $saveData['start_datetime'] = $card_info['start_datetime'];
        $saveData['end_datetime']   = $card_info['end_datetime'];
    }

    // 绑定条件
    $saveMap['user_id']   = 0;
    $saveMap['use_state'] = 1;
    $saveMap['card_id']   = $data['card_id'];

    $trans = M();
    $trans->startTrans();

    $saveRes = $m->where($saveMap)->limit($data['num'])->save($saveData);

    if(!$saveRes)
    {
        $return_data['msg']  = '卡券领取失败';
        return $return_data;
    }

    if($saveRes != $data['num'])
    {
        $trans->rollback();
        $return_data['msg']  = '卡券库存不足';
        return $return_data;
    }

    $idAry = $m->where($saveData)->limit($data['num'])->getField('id',true);

    $trans->commit();
    $return_data['code'] = 1;
    $return_data['msg']  = 'success';
    $return_data['data'] = $idAry;

    return $return_data;
}

/*合并收益到账单数据*/
function chickenTodayfeedDeliveryMergeBillByUser($user_id)
{
    $m   = M('MergeBill');
    $c_m = M('ChickenTodayfeedDelivery');

    // 查询结算合并

    $map = array();
    $map['state'] = 3;
    $map['is_merge'] = 2;
    $map['user_id']  = $user_id;
    $c_list = $c_m->where($map)->limit(10)->select();

    $c_come_from = 1;
    if($c_list)
    {
        $c_add_list = array();
        foreach ($c_list as $ck=>$cv)
        {
            $c_tmp = array();
            $c_tmp['oid']         = $cv['id'];
            $c_tmp['come_from']   = $c_come_from;
            // 是否合并
            if($m->where($c_tmp)->find())
            {
                $c_m->where('id=' . $cv['id'])->setField('is_merge', 1);
                continue;
            }

            $c_tmp['user_id']     = $cv['user_id'];
            $c_tmp['create_time'] = $cv['created_at'];
            $c_tmp['create_year'] = date('Y',$c_tmp['create_time']);
            $c_tmp['create_year_month'] = date('Y-m',$c_tmp['create_time']);
            $c_tmp['create_date'] = date('Y-m-d',$c_tmp['create_time']);

            $c_tmp['delivery_time'] = strtotime($cv['delivery_date']);
            $c_tmp['delivery_year'] = date('Y',$c_tmp['delivery_time']);
            $c_tmp['delivery_year_month'] = date('Y-m',$c_tmp['delivery_time']);
            $c_tmp['delivery_date'] = date('Y-m-d',$c_tmp['delivery_time']);
            $c_add_list[] = $c_tmp;
        }
        $c_res = $m->addAll($c_add_list);
    }
    if($c_res) return true;
}

/*合并提现到账单数据*/
function withdrawalsMergeBillByUser($user_id)
{
    $m   = M('MergeBill');
    $w_m = M('Withdrawals');

    // 查询提现合并
    $w_come_from = 2;
    $oid_w       = $m->where('user_id='.$user_id.' and come_from='.$w_come_from)->order('oid desc')->getField('oid');
    if($oid_w)
    {
        $w_list = $w_m->where('user_id='.$user_id.' and id > '.$oid_w)->limit(10)->select();
    }
    else
    {
        $w_list = $w_m->where('user_id='.$user_id)->limit(10)->select();
    }

    if($w_list)
    {
        $w_add_list = array();
        foreach ($w_list as $wk=>$wv)
        {
            $w_tmp = array();
            $w_tmp['oid']         = $wv['id'];
            $w_tmp['user_id']     = $wv['user_id'];
            $w_tmp['come_from']   = $w_come_from;

            $w_tmp['create_time'] = $wv['created_at'];
            $w_tmp['create_year'] = date('Y',$wv['created_at']);
            $w_tmp['create_year_month'] = date('Y-m',$wv['created_at']);
            $w_tmp['create_date'] = date('Y-m-d',$wv['created_at']);

            $w_tmp['delivery_time'] = $wv['created_at'];
            $w_tmp['delivery_year'] = date('Y',$wv['created_at']);
            $w_tmp['delivery_year_month'] = date('Y-m',$wv['created_at']);
            $w_tmp['delivery_date'] = date('Y-m-d',$wv['created_at']);
            $w_add_list[] = $w_tmp;
        }
        $w_res = $m->addAll($w_add_list);
    }
    if($w_res) return true;
}

// 买单下发卡券
function orderGiveCard($order_sn)
{
    $m = M('ChickenOrder');
    $map = array();
    $map['pay_state']    = 2;
    $map['state']        = 3;
    $map['order_sn']     = $order_sn;
    $map['chicken_type'] = 1;
    $v = $m->field('id,user_id,num,chicken_type_id')->where($map)->find();

    $card_list = getGiveCardListByChickenTypeId($v['chicken_type_id']);
    if(!$card_list) return;

    $tmp = array();
    $tmp['use_state'] = 1;
    $tmp['id']        = $v['id'];
    $tmp['user_id']   = $v['user_id'];

    foreach ($card_list as $cv)
    {
        $tmp['num'] = $v['num']*$cv['num'];
        $tmp['card_id'] = $cv['card_id'];

        // 查询是否已经补发
        $issue_map = array();
        $issue_map['user_id'] = $tmp['user_id'];
        $issue_map['reason_source_id'] = $tmp['id'];
        $issue_map['unique_code'] = $tmp['id'].'_'.$tmp['card_id'];
        if(M('CardIssue')->where($issue_map)->find()) continue;

        // 发卡
        $take_res = takeYzCard($tmp);

        // 流水记录
        $tmp['reason_source_id'] = $issue_map['reason_source_id'];
        $tmp['unique_code']      = $issue_map['unique_code'];
        $tmp['code_id_info']     = $take_res['data'];
        $tmp['reason_type']      = 1;
        $tmp['reason_narration'] = '套餐认购赠送';

        if($take_res && $take_res['code']==1)
        {
            $tmp['state'] = 1;
        }
        else
        {
            $tmp['state'] = 2;
            $tmp['err_code'] = $take_res['msg'];
        }
        // 记录
        addIssueCardRecord($tmp);
    }
}

function testEmoji($str)
{
    $text = json_encode($str); //暴露出unicode
    return preg_match("/(\\\u[ed][0-9a-f]{3})/i",$text);
}


/*充值回调验签*/
function payNotifyUrlSignCheck($order_sn,$sign)
{
    if(md5(md5($order_sn))==$sign) return true;
}