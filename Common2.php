<?php

class Common
{

    public function formatDate($sTime){
        $cTime      =   time();
        $dTime      =   $cTime - $sTime;
        $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
        $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));

        if($dTime < 10){
            return date('H:i:s',$sTime);
        }elseif( $dTime < 60 ){
            return date('H:i:s',$sTime);
        }elseif( $dTime < 3600 ){
            return date('H:i:s',$sTime);
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return date('H:i:s',$sTime);
        }elseif( $dDay > 0 && $dDay<=1 ){
            return date("m-d",$sTime);
        }elseif($dYear==0){
            return date("m-d",$sTime);
        }else{
            return date("Y-m-d",$sTime);
        }
    }

    public function friendlyDate($sTime,$type = 'mohu',$alt = 'false') {
        if (!$sTime)
            return '';
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime      =   time();
        $dTime      =   $cTime - $sTime;
        $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
        //$dDay     =   intval($dTime/3600/24);
        $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));

        //normal：n秒前，n分钟前，n小时前，日期

        if($type=='normal'){
            if( $dTime < 60 ){
                if($dTime < 10){
                    return '刚刚';    //by yangjs
                }else{
                    return intval(floor($dTime / 10) * 10)."秒前";
                }
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
                //今天的数据.年份相同.日期相同.
            }elseif( $dYear==0 && $dDay == 0  ){
                //return intval($dTime/3600)."小时前";
                return '今天'.date('H:i',$sTime);
            }elseif($dYear==0){
                return date("m月d日 H:i",$sTime);
            }else{
                return date("Y-m-d H:i",$sTime);
            }
        }elseif($type=='mohu'){
            /*if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay)."天前";
            }elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . '周前';
            }elseif( $dDay > 30 ){
                return intval($dDay/30) . '个月前';
            }*/

            /*
            if($dTime < 10){
                return "刚刚";
            }elseif( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif( $dDay > 0 && $dDay<=7 ){
                return intval($dDay)."天前";
            }elseif( $dDay > 7 &&  $dDay <= 30 ){
                return intval($dDay/7) . '周前';
            }elseif( $dDay > 30 && $dYear==0){
                return intval($dDay/30) . '个月前';
            }else{
                return date("Y/m/d",$sTime);
            }
            */
            if($dTime < 10){
                return "刚刚";
            }elseif( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif( $dDay > 0 && $dDay<=1 ){
                return intval($dDay)."天前";
            }elseif($dYear==0){
                return date("m-d",$sTime);
            }else{
                return date("Y-m-d",$sTime);
            }

            //full: Y-m-d , H:i:s
        }elseif($type=='full'){
            return date("Y-m-d , H:i:s",$sTime);
        }elseif($type=='ymd'){
            return date("Y-m-d",$sTime);
        }else{
            if( $dTime < 60 ){
                return $dTime."秒前";
            }elseif( $dTime < 3600 ){
                return intval($dTime/60)."分钟前";
            }elseif( $dTime >= 3600 && $dDay == 0  ){
                return intval($dTime/3600)."小时前";
            }elseif($dYear==0){
                return date("Y-m-d H:i:s",$sTime);
            }else{
                return date("Y-m-d H:i:s",$sTime);
            }
        }
    }

    //字符串截取
    public function truncate_utf8_string($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
        {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT),
                '0'))
            {
                if ($length < 1.0)
                {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen)
        {
            $result .= $etc;
        }
        return $result;
    }

    //iso表情过滤
    public function emojiExp($str){
	    return $str;
        $str = urlencode($str);
        $str = str_replace("%", "\\x", $str);
        $strs1 = "/\\\\(xf0|xe2)\\\\x(8|9).{1}\\\\x(8|9|a|b).{1}/i";
        $strs3 = "/\\\\(xf0|xe2)\\\\x(8|9).{1}\\\\x(8|9).{1}\\\\x(8|9|a|b).{1}/i";

        //处理表情编码为4的  匹配出来替换
        if(preg_match($strs3,$str,$brr)){

            $str12 =  preg_replace($strs3,"",$str);
            //处理表情编码为3的
            if(preg_match($strs1,$str12,$arr)){
                $str12 =  preg_replace($strs3,"",$str12);
            }
        }else if(preg_match($strs1,$str,$crr)){

            $str12 =  preg_replace($strs1,"",$str);
        }
        $str12 = $str12?$str12:$str;
        $text=str_replace("\\x", "%", $str12);
        $str = urldecode($text);
        return $str;
    }

    //百度推送
    public function BaiduPush($urls = array()){
    	return true;
        /*$urls = array(
            'http://www.1111.com',
        );*/
        $api = 'http://data.zz.baidu.com/urls?site=www.111.com&token=';

        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }

    /**
     * 关联关键词替换
     * @param txt $string 原字符串
     * @param replacenum $int 替换次数
     * @return string 返回字符串
     */
    public function keylinks($txt, $replacenum = '20')
    {
        $txts = $txt;
        $sql = 'select id,name from category where `index`>0';
        $data = Category::model()->findAllBySql($sql);
        foreach($data as $model){
            $linkdatas[] = array($model->name,Yii::app()->createUrl('product/cate',array('id'=>$model->id)));
        }

        if ($linkdatas) {
            $word = $replacement = array();
            foreach ($linkdatas as $v) {
                $word[] = '/(?!(<[^p]*?))' . preg_quote($v[0], '/') . '(?!(.*?[^p]>))/';//改进后将不匹配img里的内容
                $replacement[] = '<a href="' . $v[1] . '" target="_blank" class="keylink">' . $v[0] . '</a>';
            }
            $preData = array(
                'word'=>$word,
                'replacement'=>$replacement
            );
        }

        $txt = str_replace('</p>', "</p>\n", $txt);//预处理
        $txt = preg_replace($preData['word'], $preData['replacement'], $txt, $replacenum);

        return $txt?$txt:$txts;
    }

    /*
    * 异步调用
    * get参数，status是否返回执行结果，host
    */
    public function AsyExe($get,$status='0',$host='zdm.1111.com'){
	/*$fp = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $out = "GET $get HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);

            //忽略执行结果
            if($status){
                while (!feof($fp)) {
                    //echo fgets($fp, 128);
                }
            }
	        //usleep(100000);
            sleep(1);
            fclose($fp);
        }*/
	    $fp = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            stream_set_blocking($fp,0);
            $out = "GET $get HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);

            $status ? sleep(1) : usleep(100);

            fclose($fp);

        }
    }


    /**
     * 返回前端统一数据接口
     */
    public function postData($data,$status='true',$code='0',$message='操作成功',$limit=0,$version='1.0'){
        if( gettype($status)=='boolean') $status = $status?'true':'false';
        if( gettype($status)=='integer') $status = $status==0?'true':'false';
        $data = array(
            'success'           => $status,
            'resultCode'        => $code,
            'errorMsg'          => $message,
            'result'            => $data,
            'limit'             => $limit,
            'version'           => $version,
        );
        exit(CJSON::encode($data));
    }

	/**
	 * 返回前端统一数据接口 加上limit
	 */
	public function postDataLimit($data, $limit){
		$this->postData($data,'true','0','操作成功',$limit);
	}

	public function postDLimit($data, $limit){
		$this->postD($data,'true','0','操作成功',$limit);
	}

    /**
     * 返回前端统一数据接口
     */
    public function postD($data,$status='true',$code='0',$message='操作成功',$limit=0,$version='1.0'){

        $data = array(
            'success'           => $status,
            'resultCode'        => $code,
            'errorMsg'          => $message,
            'result'            => $data,
            'limit'             => $limit,
            'version'           => $version
        );

        echo (CJSON::encode($data));
    }

    public function error($message,$status=-1){
        $this->postData(array(),'false',$status,$message);
    }


    /**
     * 订单号过滤
     */
    public function OrderFileter($orderid=''){
        if($orderid){
            return preg_replace('/[^0-9]/','',$orderid);
        }
    }

    function is_mobile(){

        // returns true if one of the specified mobile browsers is detected

        $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
        $regex_match.=")/i";
        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }


    /**
     * @param $string
     * @return array
     * 解析css样式
     */
    private function parseStyle($string){
        $_result = array();
        preg_match_all('/<p([^>]*)>/i',$string,$result);
        if(isset($result[1]) && count($result[1])){
            foreach ($result[1] as $k => $v ){
                preg_match('/style\s*=\s*(\'|")(.*?)\\1/i',$v,$match_sub);
                if(isset($match_sub[2]) && count( $match_sub[2] = explode(';',$match_sub[2]))){
                    foreach ($match_sub[2] as $item) {
                        $item = explode(':',$item);
                        !isset($item[0]) || !trim($item[0]) || $_result[str_replace('-','_',strtolower(trim($item[0])))] = isset($item[1])?trim($item[1]):'';
                    }
                }
            }
        }
        return $_result;
    }

    /**
     * @param $attr
     * @return array
     * 解析标签属性
     */
    private function parseAttr($attr){
        if(substr($attr,0,1)!='<'){
            $attr = '<tag '.$attr;
        }

        $key = md5($attr);
        if(!Yii::app()->filecache->get($key)){
            preg_match('/<(\w+)([^>]*)(?:\/>|>)?/',$attr,$match);
            $v = preg_split('/\s+/',$match[2]);
            $result = array();
            foreach ($v as $_k2 => $_v2){
                $_v2 = explode('=',$_v2);
                if(isset($_v2[0]) && preg_match('/[\w\d-]+/i',trim($_v2[0]))){
                    $_v2[1] = str_replace('"/','',$_v2[1]);
                    $result[str_replace('-','_',str_replace(array('\'','"'),'',trim($_v2[0])))] = str_replace(array('\'','"'),'',$_v2[1]);
                }
            }


            if(
            	isset(
		            $result['src']) && ( ($result['data_width'] <= 0) ||
	                                     ($result['data_height'] <= 0) ||
	                                     ($result['data_ratio'] <= 0) ||
	                                     !($result['data_width']) ||
	                                     !($result['data_height'])||
	                                     !($result['data_ratio']))
            ){

                list($width,$height,$type) = getimagesize($result['src']);
                $result['data_width'] = $width;
                $result['data_height'] = $height;
                $result['data_img_type'] = $type;
                $result['data_ratio'] = $width / $height;
            }

            $data = array(
                'src'=>$result['src'],
                'data_img_type'=>intval($result['data_img_type']),
                'data_original'=>$result['data_original'] ? $result['data_original'] : "",
                'data_width'=> intval($result['data_width']),
                'data_height'=> intval($result['data_height']),
                'data_ratio'=> $result['data_ratio'] ? $result['data_ratio'].'' : "",
            );
            Yii::app()->filecache->set($key,$data,864000);
            return $data;
        }

        return Yii::app()->filecache->get($key);
    }
    /**
     * @param $src
     * @return string
     * 解析视频地址
     */
    private function parseVideoUrl($src){
        $src = trim($src);
        if(strtolower(substr($src,0,4))!=='http'){
            $src = '';
        }
        if( strpos($src,"v.qq.com")!==false || strpos($src,"video.qq.com")!==false || strpos($src,"imgcache.qq.com")!==false ){
            $_src = explode('?',$src);
            $_src = explode('&',isset($_src[1])?$_src[1]:'');
            $vid = '';
            foreach ($_src as $k => $v ){
                $v = explode('=',$v);
                if(strtolower($v[0])=='vid'){
                    $vid = $v[1];
                    break;
                }
            }
            $src = 'http://v.qq.com/iframe/player.html?vid='.$vid.'&auto=0';
        }elseif(strpos($src,"youku.com")!==false){
            preg_match('/http:\/\/player.youku.com\/player.php\/sid\/(.+)\/v.swf/',$src,$src_match);
            $src = 'http://player.youku.com/embed/'.$src_match[1];
        }elseif(strpos($src,"sohu.com")!==false){
            $src = $src;
        }else{
            $src = '';
        }
        return $src;
    }

	/**
	 * RGB转 十六进制
	 * @param $rgb RGB颜色的字符串 如：rgb(255,255,255);
	 * @return string 十六进制颜色值 如：#FFFFFF
	 */
	public function RGBToHex($rgb){

		$regexp = "/\(([0-9]{0,3})\,\s*([0-9]{0,3})\,\s*([0-9]{0,3})\)/";
		$re = preg_match($regexp, $rgb, $match);
		$re = array_shift($match);

		$hexColor = "#";
		$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
		for ($i = 0; $i < 3; $i++) {
			$r = null;
			$c = $match[$i];
			$hexAr = array();
			while ($c > 16) {
				$r = $c % 16;
				$c = ($c / 16) >> 0;
				array_push($hexAr, $hex[$r]);
			}
			array_push($hexAr, $hex[$c]);
			$ret = array_reverse($hexAr);
			$item = implode('', $ret);
			$item = str_pad($item, 2, '0', STR_PAD_LEFT);
			$hexColor .= $item;
		}

		return $hexColor;
	}

    /**
     * @param $html
     * @return array
     * html转native
     */
    public function htmlToNative($html,$uid,$ver=0, $eventid=0){

    	//颜色修改
    	if(preg_match_all('/(rgb\(([0-9]{0,3}),\s*([0-9]{0,3}),\s*([0-9]{0,3})\))/', $html, $a)){
		    foreach ($a[0] as $v){
		    	$html = str_replace($v, $this->RGBToHex($v), $html);
		    }
	    }
	    $preg_span = '/<span((.*?)(color:\s*((\S){7}))(.*?))>(.*?)<\/span>/';

	    if(preg_match($preg_span, $html, $a)){

		    //这里可以添加转换成任意标签  比如 <span color="#FE5341">aaa</span> ==> <font color="#FE5341">aaa</font> <span((((?!>).)*?)(color:\s*((\S){7}))(.*?))>(.*?)<\/span>
		    if(strip_tags($a[7])){
			    $html = preg_replace('/<span((((?!>).)*?)(color:\s*((\S){7}))(.*?))>(.*?)<\/span>/', '<font color="$5">$8</font>', $html);
		    }
	    }

		//链接
		if(preg_match_all('/<a\s*href="(.*?)"/', $html, $a)){
			foreach ($a[1] as $v){
				$html = str_replace($v, $this->scheme($v), $html);
			}

			$html = preg_replace('/<a\s*href="(.*?)"\s*(.*?)>(.*?)<\/a>/', '<a href="$1"><font color="#4A90E2">$3</font></a>', $html);

		}

        $_ = $separator = '#11#';
        $index = 0;
       // $html = preg_replace("/<\/iframe>(&nbsp;)+/","</iframe>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$html);

        $html = preg_replace("/<\/iframe>(&nbsp;)+/","</iframe>",$html);

	    if($ver >= 6.0){
		    $html  = strip_tags($html,'<a><font><p><img><embed><iframe><titlesmall><h6><strong><ul><ol><li><hr/><h3>');
	    }elseif($ver >= 4.0){
            $html  = strip_tags($html,'<p><img><embed><iframe><titlesmall><h6><strong><ul><ol><li><hr/><h3>');
        } else if($ver >= 3.0){
	        $html  = strip_tags($html,'<p><img><embed><iframe><titlesmall><h6><strong>');
        }else{
            $html  = strip_tags($html,'<p><img><embed><iframe><titlesmall><h6>');
        }

	    $html   = str_replace(array('&lt;','&gt;','&nbsp;','&#39;','&amp;'),array('<','>',' ','\'','&'),$html);
	    $html   = html_entity_decode($html); // 将html实体转换成html对应标签
	    $html   = preg_replace(array('/&nbsp;/i','/[\t\r\n]/i'),'',$html);

	    $html   =  str_replace(
		    array('<li><p>','</p></li>'),
		    array('<li>','</li>'),
		    $html
	    );

        $html  = str_replace(
            array('<p','<img','<embed','<iframe','<titlesmall','<h6','<ul','<ol','<hr','<h3'),
            array($_.'<p',$_.'<img',$_.'<embed',$_.'<iframe',$_.'<titlesmall',$_.'<h6',$_.'<ul',$_.'<ol',$_.'<hr',$_.'<h3'),
            $html
        );

        $html   = explode($separator,$html);
        $img_index = 0;//图片定位索引

        foreach($html as $k=>$v){
            $result[$index]['hasstrong'] = 0;
            //解析图片
            if(preg_match('/<img.*?src="(.*?)".*?>/',$v,$match)) {
                $result[$index]['type'] = 'img';
                $result[$index]['content']['data'] = $match[1];
                preg_match('/<img(.*?)>/',$v,$match);
                $result[$index]['style'] = $this->parseAttr($match[1]);
                $result[$index]['style']['next_is_img'] = $this->_next_is_img($html[$k+1]);
                if($eventid && $eventid<2231 ){
	                $result[$index]['style']['next_is_img'] = 1;
                }
                $result[$index]['img_index'] = $img_index;
                $index++;
                $img_index++;
            }
            //h3解析小标题
            elseif(preg_match('/<h3.*?>(.*?)<\/h3>/i',$v,$match)) {

	            $result[$index]['type'] = 'h3';
	            $result[$index]['style']['style'] = '';

	            if($ver >= 6.0){
		            $result[$index]['content']['data'] = strip_tags($match[1], '<strong><font><a>');
	            }else{
		            $result[$index]['content']['data'] = strip_tags($match[1]);
	            }
	            $index++;
	            continue;
            }
            //hr解析hr
            elseif(preg_match('/<hr\/>/i',$v,$match)) {
	            $result[$index]['type']             = 'hr';
	            $result[$index]['style']['style']   = 'default';
	            $result[$index]['content']['data']  = '';
	            $index++;
	            continue;
            }
            //ul解析
            elseif(preg_match('/<(ul|ol)[^>]*style="(.*?)"[^>]*>(.*?)<\/\\1>/i',$v,$match)) {

	            if($match[3]){
		            $_li  = str_replace(
			            array('<li'),
			            array($_.'<li'),
			            $match[3]
		            );
		            $_li   = explode($_,$_li);
		            if($_li){
			            $num = 0;
			            foreach ($_li as $k2=>$v2){
				            if(!$v2){
					            continue;
				            }
				            if(preg_match('/<li>(.*?)<\/li>/',$v2,$tt)){
					            $num++;
					            if($match[2] == 'list-style-type: disc;'){
						            $num = '•';
					            }else if($match[2] != 'list-style-type: decimal;'){
						            $num = "";
					            }else{
						            $num = $num .'.';
					            }

					            $result[$index]['type'] = 'ul';
					            $result[$index]['style']['style'] = 'default';
					            if($ver >= 6.0){
						            $result[$index]['content']['data'] = strip_tags($tt[1], '<strong><font><a>');
					            }else{
						            $result[$index]['content']['data'] = strip_tags($tt[1]);
					            }

					            $result[$index]['child_num'] = $num.'';
					            $index++;
				            }
			            }
		            }
	            }

	            continue;
            }
            //解析小标题
            elseif(preg_match('/<(titlesmall|h6)[^>]*>(.*?)<\/\\1>/i',$v,$match)) {
                $result[$index]['type'] = 'txt';
                $result[$index]['style']['style'] = 'h6';

	            if($ver >= 6.0){
		            $result[$index]['content']['data'] = strip_tags($match[2], '\'<strong><font><a>\'');
	            }else{
		            $result[$index]['content']['data'] = strip_tags($match[2]);
	            }
                $index++;
                continue;
            }
            //解析视频地址
            elseif(preg_match('/<embed.*?src="(.*?)".*?>/',$v,$match)) {
                $src = $this->parseVideoUrl($match[1]);
                if($src){
                    $result[$index]['type'] = 'video';
                    $result[$index]['style']['style'] = 'default';
                    $result[$index]['content']['data'] = $src?$src:'';
                    $index++;
                }
            }
            //解析卡片
            elseif(preg_match('/<iframe.*?src="(.*?)".*?>/i',$v,$match)) {

                //解析优惠卷
                if(preg_match('/<iframe.*?data-coupon-cid="(.*?)".*?>/i',$v,$ma)){

                    if($ma['1']){
                        $result[$index]['content'] = $this->_couponInfo($ma['1'],$uid);
                        $result[$index]['type'] = 'coupon';
                        $result[$index]['style']['style'] = 'default';
                        $index++;
                    }
                }else{
                    //解析链接data-link
                    if(preg_match('/<iframe.*?data-link="(.*?)".*?>/i',$v,$ma)){
                        $link = array();
                        parse_str(base64_decode($ma[1]),$link);
                        $link_data = $this->_formatLink($link['link']);
                        if($link_data){
                            $result[$index]['content']          = $link_data;
                            $result[$index]['link']             = $link_data;
                            $result[$index]['type']             = 'link';
                            $result[$index]['style']['style']   = 'default';
                            $index++;
                        }

                    }else{

                        $card = $this->actionGetProductCard($match[1],$uid);
                        if($card){
                            $result[$index]['content'] = $card;
                            $result[$index]['type'] = 'card';
                            $result[$index]['style']['style'] = 'default';
                            $index++;
                        }
                    }

                }
            }

	        if($ver >= 6.0){
		        $txt = strip_tags($v,'<strong><font><a>');
	        }elseif($ver >= 3.0){
                $txt = strip_tags($v,'<strong>');
            }else{
                $txt = strip_tags($v);
            }
            if($txt){
                $style = $this->parseStyle($v);
                $result[$index]['type'] = 'txt';
                $result[$index]['style'] = empty($style)?array('style'=>'default'):$style;
                //$result[$index]['content']['data'] = str_replace(" ","",preg_replace('/ style="(.*?)"/','',$txt));
                $result[$index]['content']['data'] = preg_replace('/ style="(.*?)"/','',$txt);

                if(strpos($txt,'<strong>') > -1){
                    $result[$index]['hasstrong'] = 1;
                }
                $index++;

            }
        }
        return $result;
    }

    //小程序url处理
	private function _wxcodeUrl($url){
		if(IS_DEV == 1){
			$base_url = 'https://wx.111.com';
		}else{
			$base_url = 'https://api.111.com';
		}

		preg_match("/(\d+)/", $url, $maths);
		if($maths[1]){
			$str = "/{$maths[1]}.html";
			$url = str_replace($str, "", $url);
			$url .=   "?id=".$maths[1];

			$url = str_replace('http://www.111.com',$base_url, $url);
		}

		return $url;
	}

    //判断下一个是不是图片
	private function _next_is_img($txt){
    	if($txt && preg_match('/<img(.*?)>/',$txt,$match)){
    		return 1;
	    }
	    return 0;
	}

    //将连接转成对应的Scheme
	public function scheme($url){

		$platform = trim(Yii::app()->request->getParam("platform"));
		if(strtolower($platform) == "ios"){
			$scheme = $this->_androidScheme($url);
		}else{
			$scheme = $this->_androidScheme($url);
		}

		return $scheme? $scheme : $url;

	}

    //解析链接
    public function _formatLink($link){
        $link_data = array();
        if($link['title']){
            $link_data = $link;
            $link_data['type']                      = $link_data['type'] ? $link_data['type'] : 0;
            $link_data['start_price']               = $this->formatPrice($link_data['start_price']);
            $link_data['price']                     = $this->formatPrice($link_data['price']);
            $link_data['starttime']                 = $link_data['starttime']   ? strtotime($link_data['starttime'])    :     $link_data['starttime'];
            $link_data['endtime']                   = $link_data['endtime']     ? strtotime($link_data['endtime'])      :     $link_data['endtime'];
            $link_data['type']                      = $link_data['ismiaosha']   ? 2  : $link_data['type'];
            $link_data['android_scheme']            = $this->androidScheme($link_data['url']);
            $link_data['ios_scheme']                = $this->iosScheme($link_data['url']);
        }

        return $link_data;
    }

    //字符串过滤
    public function filterStrings($str){
        static $purifier;
        if(!$purifier){
            $purifier = new CHtmlPurifier();
        }

        $str = htmlspecialchars($purifier->purify(($str)));
        return $str;
    }

    //获取微信access_token
	public function getToken($appid='', $appsecret=''){
		$options = array(
			//填写你设定的key
			'token'     => 'red_xiao',
			//填写高级调用功能的app id, 请在微信开发模式后台查询
			'appid'     => $appid ? $appid : 111,
			//填写高级调用功能的密钥
			'appsecret' => $appsecret ? $appsecret : 111,
		);
		$weixinObj = new Wechat($options);
		return $weixinObj->checkAuth();
	}

    //设置队列
    public function setQueue($message){
        // 向队列发送任务，让队列慢慢去执行
        $client = stream_socket_client("tcp://io.111.com:1236", $err_no, $err_msg);
        if($client)
        {
            fwrite($client, $message);
            fclose($client);

        }

    }

	//设置队列2017-03-28
	private static $client;
	public function setNewQueue($str){

		if(!self::$client){
			self::$client = stream_socket_client("tcp://io.111.com:1236", $err_no, $err_msg);
		}

		// 一个邮件任务
		$message = array(
			'class'     => 'Job',
			'method'    => 'call',
			//'time'      => $time,
			'args'      => $str,
		);
		// 数据末尾加一个换行，使其符合Text协议。使用json编码
		$message = json_encode($message)."\n";
		// 向队列发送任务，让队列慢慢去执行

		if(self::$client)
		{
			fwrite(self::$client, $message);

			//exit($err_msg);
		}

	}

	public function __destruct() {
		// TODO: Implement __destruct() method.
		if(self::$client){
			fclose(self::$client);
		}
	}

    //价格处理
    public function formatPrice($price){
	    $price = $price-0;
	    return $price ? round($price, 2).''  : "";
    }

    //时间换算
    public function topicDown($unix_timestamp,$btn='') {
        $date = $unix_timestamp-time();
        if($date<0) return $btn ? $btn : '';
        $days = $date/60/60/24;

        if(intval($days)>0){
            return '距结束还有'.intval($days).'天';
        }

        $hour = $days*24;
        $hours = $hour;
        if(intval($hours)>0){
            return '距结束还有'.intval($hours).'小时';
        }

        $minute = $hours*60;
        $minutes = $minute;
        if(intval($minutes)>0){
            return '距结束还有'.intval($minute).'分钟';
        }
        $second = $minutes*60;
        if(intval($second)>0){
            return '距结束还有'.intval($second).'秒';
        }
        return '';
    }

    //过滤空格 换行
    public function flterln($str){
        if(is_string($str)){
            return str_replace(PHP_EOL, '', strip_tags($str));
        }
        return $str;

    }

    //打印增加或修改错误
    public function getError($obj){
        if($obj->save() == false){
            var_dump($obj->errors);exit;
        }
    }

    //格式化json
    public function evalJson($data){
        return json_decode(CJSON::encode($data),TRUE);
    }

    //价格处理
    public function priceFormat($price){
        return ($this->price2Float($price) - 0) .'';
    }

    //倒计时处理
    public function countdown2($unix_timestamp, $is_return=1) {
        $date = $unix_timestamp-time();
        $day = $date/60/60/24;
        $days = (int)$day;
        $hour = $date/60/60 - $days*24;
        $hours = (int)$hour;
        $minute = $date/60 - $days*24*60 - $hours*60;
        $minutes = (int)$minute;
        $second = $date - $days*24*60*60 - $hours*60*60 - $minutes*60;
        $seconds = (int)$second;
        //$result = array($days,$hours,$minutes,$seconds);
        //$result = $days.'天'.$hours.'小时'.$minutes.'分钟';
        if($days > 0){
	        if($is_return){
		        return $days.'天';
	        }
	        $result = ''.$days.'天'.$hours.'小时'.$minutes.'分钟';
        }else if($hours > 0){
	        if($is_return){
		        return $hours.'小时';
	        }

            $result = $hours.'小时'.$minutes.'分钟'.$second.'秒';
        }else if($second > 0){

        	if($minutes > 0 && $is_return){
        		return $minutes.'分钟';
	        }else if($second > 0 && $is_return){
		        return $second.'秒';
	        }
            $result = $minutes.'分钟'.$second.'秒';
        }else{
            $result = '0分钟0秒';
        }
        return $result;
    }

    public function paramFormat($num){
        return intval($num).'';
    }

    //分词
    public function    fenci($string,$c=false){
        $dir = dirname(__FILE__).'/';
        require_once $dir.'phpanalysis/phpanalysis.class.php';

        //初始化类
        $phpAnalysis = new PhpAnalysis('utf-8', 'utf-8', true);
        $phpAnalysis->SetResultType(2);
        $phpAnalysis->differMax = true;
        $phpAnalysis->unitWord = true;

        $string = preg_replace('/(\&(.*?)\;)/i','',strip_tags($string));
        //设置分词字符串
        $phpAnalysis->SetSource( $string );
        //开始进行分词
        $phpAnalysis->StartAnalysis( true );
        $string = $phpAnalysis->GetFinallyResult(' ');
        //返回分词结果
        if($c) $string = preg_replace('/(\s[\x{4e00}-\x{9fa5}]{1}\s)|(\s[\d]{1,2}\s)/ui',' ',$string);
        return $string;
    }

    //合并搜索结果 数组合并
    public function  new_array_merge($arr=array(),$brr=array()){

        foreach($arr as $k=>$v){
            if(isset($brr[$k])){
                $brr[$k] = $brr[$k]+$v;
            }else{
                $brr[$k] = $v;
            }
        }
        return $brr;
    }

    /**
     * 价格格式化成0.00格式
     *
     * @param  float    $price
     * @return float    $price_format
     */
    public function price2Float($price) {
        $price = floatval($price);
        $int = intval($price);
        if($int == $price) return $int;
        return rtrim(number_format($price,2,'.',''),'0');
    }

    /**
     * 折扣格式化成1位小数
     * @param $discount
     * @return string
     */
    public function discountFormat($discount) {
        $discount = floatval($discount);
        return number_format($discount,1,'.','');
    }

    /**
     * 字符串截取
     * @param  [string] $string [description]
     * @param  [int] $length [description]
     * @param  string $dot    [description]
     * @return [str]         [description]
     */
    public function cutstr($string, $length, $dot = ' ...') {
        defined('CHARSET')||define('CHARSET','UTF-8');
        if(strlen($string) <= $length) {
            return $string;
        }

        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

        $strcut = '';
        if(strtolower(CHARSET) == 'utf-8') {

            $n = $tn = $noc = 0;
            while($n < strlen($string)) {

                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1; $n++; $noc++;
                } elseif(194 <= $t && $t <= 223) {
                    $tn = 2; $n += 2; $noc += 2;
                } elseif(224 <= $t && $t <= 239) {
                    $tn = 3; $n += 3; $noc += 2;
                } elseif(240 <= $t && $t <= 247) {
                    $tn = 4; $n += 4; $noc += 2;
                } elseif(248 <= $t && $t <= 251) {
                    $tn = 5; $n += 5; $noc += 2;
                } elseif($t == 252 || $t == 253) {
                    $tn = 6; $n += 6; $noc += 2;
                } else {
                    $n++;
                }

                if($noc >= $length) {
                    break;
                }

            }
            if($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            $_length = $length - 1;
            for($i = 0; $i < $length; $i++) {
                if(ord($string[$i]) <= 127) {
                    $strcut .= $string[$i];
                } else if($i < $_length) {
                    $strcut .= $string[$i].$string[++$i];
                }
            }
        }

        $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        $pos = strrpos($strcut, chr(1));
        if($pos !== false) {
            $strcut = substr($strcut,0,$pos);
        }
        return $strcut.$dot;
    }



    /**
     * 把AR查询结果转为数组
     * @param $object
     * @return mixed
     */
    public  function    toArray($object){
        return   json_decode(CJSON::encode($object),true);
    }

    //过滤不可见字符
    public function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = array();

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
            $non_displayables[] = '/%7f/i';	// url encoded 127
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ($count);

        return $str;
    }

    public function createExcel($data,$file_name = '数据导出.xls'){
        $colms = array('A','B','C','D','E','F','G',
            'H','I','J','K','L','M','N',
            'O','P','Q','R','S','T',
            'U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG');
        $objPHPExcel = new PHPExcel();
        $sheet =  $objPHPExcel->setActiveSheetIndex(0);
        if(!empty($data)){
            $row_number = 1;
            foreach($data as $row_data){
                $col_number = 0;
                foreach($row_data as $col){
                    $sheet -> setCellValueExplicit($colms[$col_number].$row_number,str_replace('=','',addslashes($col)),PHPExcel_Cell_DataType::TYPE_STRING);
                    $col_number++;
                }
                $row_number++;
            }
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');exit;
        }else{
            exit('<meta charset="utf-8">数据为空');
        }

    }


    private function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
        return $num ? $num : 0;
    }

    //产品抓取过滤
    public function filterDetail($content){
        $str = preg_replace( "@<script(.*?)</script>@is", "", $content );
        $str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str );
        $str = preg_replace( "@<style(.*?)</style>@is", "", $str );
        return $str;
    }

    //获取支付方式
    public function getPayType(){
        return array(
            'alinew'        =>'支付宝app',
            'alinew_app'    =>'新版支付宝app',
            'ali'           =>'支付宝pc/h5',
            'ali_new'       =>'新版支付宝pc/h5',
            'wx_app'        =>'微信app',
            'wx_new_app'    =>'新版微信app',
            'wx'            =>'微信pc/h5',
            'wx_new'        =>'新版微信pc/h5',
            'wxcode_app'    =>'微信小程序'
        );
    }

    //获取订单支付类型
    public function getOrderPayType($type){
        switch ($type){
            case 'alinew':
            case 'alinew_app':
            case 'ali':
            case 'ali_new':
                $type = 'ali';
                break;
            default:
                $type = 'wx';
        }
        return $type;
    }

    /**
     * 获取某一年或当前日期自然周
     * @param int $date 日期时间,不传参默认为当前时间，格式2016-8-3
     * @return string
     */
    function getWeekNum($date = ''){
        $datearr = ($date) ? getdate(strtotime($date)) : getdate();
        $year = strtotime($datearr['year'].'-1-1');
        $startdate = getdate($year);
        $firstweekday = 7-$startdate['wday'];//获得第一周几天
        //今年的第几天
        //$yday = $datearr['yday']+1-$firstweekday;//以周日开始计算
        $yday = $datearr['yday']-$firstweekday;//以周一开始计算
        return ceil($yday/7)+1;//取到第几周
    }

    //对账单获取支付类型
    private function _getPayType($type){
        if(preg_match('/wx/',$type, $a)){
            return 2;
        }else{
            return 1;
        }
    }

    //计算费率价格
    private function _getHairPrice($price, $type, $time, $data){

        return $price * $this->_getHair($type,$time, $data);
    }

    //获取费率
    private function _getHair($type,$time, $data){

        $hair = 0;
        if(preg_match('/wx/',$type, $a)){
            //微信退款时会返还
            $hair = 0.006;

        }else{
            if($data['operate_type'] == 1){
                if($time > strtotime('2018-01-01')){
                    $hair = 0.006;
                }else{
                    $hair = 0.0055;
                }
            }
        }

        return $hair;
    }

    //显示文章评级
    public function getBlogDisplayorder($type){
        switch ($type){
            case -3:
                $type = '需修改';
                break;
            case -2:
                $type = '草稿';
                break;
            case 0:
                $type = '待审核';
                break;
            case 1:
                $type = '普通';
                break;
            case 3:
                $type = '良好';
                break;
            case 5:
                $type = '优秀';
                break;
            default:
                $type = '未评级';

        }
        return $type;
    }



    public function casperJsExec($url,$shop){
        $url = escapeshellarg($url);
        //casperjs数据抓取
        putenv('PHANTOMJS_EXECUTABLE='.PHANTOMJS_PATH);//设置phantomjs路径
        exec('casperjs '.CASPERJS_PATH.$shop.'.js '.$url,$out,$status);

        if( $status==0 ){
            $out = json_decode(implode('',$out));
            $out->price = round((float)($out->price),2);
            $out->mark_price = round((float)($out->mark_price),2);
            $out->title = strip_tags( $out->title );
            if(is_array($out->pic)){
                $pic_str = '';
                $out->pic = json_decode($this->Image( $out->pic ),true);
                foreach ($out->pic as $k => $v ){
                    $pic_str .= '<img src="'.$v.'" />';
                }
                $out->detail = $pic_str . $out->detail;

            }
        }
        return array(
            'out'=> $out,
            'status'=>$status
        );
    }


    //图片替换处理 未完待续...
    public function Image($arr,$mall){
        switch($mall){
            case 'taobao':
                if($arr){
                    foreach($arr as $k=>$v){
                        $brr[$k] = str_replace('_50x50','_800x800',$v);
                    }
                }
                return json_encode($brr);
                break;
            case 'amazon':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"_SS40_")){
                            $img = explode('_SS40_',$v);
                            $brr[$k] = $img[0].'_UL1500_.jpg';
                        }else if(strpos($v,'_SX38_SY50_CR,0,0,38,50_')){
                            $img = explode('_SX38_SY50_CR,0,0,38,50_',$v);
                            $brr[$k] = $img[0].'_UL1500_.jpg';
                        }else if(strpos($v,"_US40_")){
                            $img = explode('_US40_',$v);
                            $brr[$k] = $img[0].'_UL1500_.jpg';
                        }else if(strpos($v,"_SR38,50_")){
                            $img = explode('_SR38,50_',$v);
                            $brr[$k] = $img[0].'_UL1500_.jpg';
                        }else{
                            $brr[$k] = str_replace('._SX38_SY50_CR,0,0,38,50_.','.',$v);
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'jd':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"com/n5")){
                            $brr[$k] = str_replace('com/n5','com/n0',$v);
                        }else{
                            $brr[$k] = str_replace('com/n5/s50x64_jfs','com/n0/jfs',$v);
                            $brr[$k] = preg_replace('/com\/n[0-9]{1}\//','com/n0/',$brr[$k]);
                            $brr[$k] = str_replace(array('s50x64_','!cc_50x64.jpg'),'',$brr[$k]);
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'kaola':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"thumbnail=64")){
                            $brr[$k] = str_replace('thumbnail=64','thumbnail=800',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'yhd':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"50x50")){
                            $brr[$k] = str_replace('50x50','640x400',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'suning':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"60x60")){
                            $brr[$k] = str_replace('60x60','800x800',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'gome':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"50.jpg")){
                            $brr[$k] = str_replace('50.jpg','800.jpg',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'newegg':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"neg/P80")){
                            $brr[$k] = str_replace('neg/P80','neg/P640',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'dangdang':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"x_1.jpg")){
                            $brr[$k] = str_replace('x_1.jpg','u_1.jpg',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            case 'yixun':
                if($arr){
                    foreach($arr as $k=>$v){
                        if(strpos($v,"jpg/60?")){
                            $brr[$k] = str_replace('jpg/60?','jpg/600?',$v);
                        }else{
                            $brr[$k] = $v;
                        }
                    }
                }
                return json_encode($brr);
                break;

            default:
                return json_encode($arr);
                break;
        }
    }

    //解析mall 未完待续...
    public function MallResolve($url){
        $domain = parse_url($url);
        $domain = $domain['host'];
        $url_arr = array(
            'item.taobao.com'       => array(
                'shop'=>'taobao',
                'mall'=>'淘宝',
                'logo'=>'8f4a3247-da08-4142-87a8-19eb29917716'
            ),
            'detail.ju.taobao.com'         => array(
                'shop'=>'jutaobao',
                'mall'=>'聚划算',
                'logo'=>'8f4a3247-da08-4142-87a8-19eb29917716',
	            'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/tmall.jpg'
            ),
            'www.amazon.com'        => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊美国',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.cn'         => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊中国',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.de'         => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊德国',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.es'         => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊西班牙',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.fr'         => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊法国',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.it'         => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.co.jp'      => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊日本',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.1111.com/zdm/asset/mall/amazon.jpg'
            ),
            'www.amazon.co.uk'      => array(
                'shop'=>'amazon',
                'mall'=>'亚马逊英国',
                'logo'=>'5121c226-adb9-4ae8-b2f0-9c251fcf0d26',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/amazon.jpg'
            ),
            'item.gome.com.cn'      => array(
                'shop'=>'gome',
                'mall'=>'国美',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/gome.jpg'
            ),
            'tuan.gome.com.cn'      => array(
                'shop'=>'gome',
                'mall'=>'国美',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/gome.jpg'
            ),
            'item.jd.com'           => array(
                'shop'=>'jd',
                'mall'=>'京东',
                'logo'=>'5b4fe475-3305-4086-9f3b-8691cd2818f0',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/jd.jpg'
            ),
            'www.kaola.com'         => array(
                'shop'=>'kaola',
                'mall'=>'网易考拉',
                'logo'=>'c0cf8c33-d076-438c-88ab-b58d344b6a54',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/kaola.jpg'
            ),
            'www.newegg.cn'         => array(
                'shop'=>'newegg',
                'mall'=>'新蛋',
            ),
            'product.suning.com'    => array(
                'shop'=>'suning',
                'mall'=>'苏宁',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/suning.jpg'
            ),
            'item.yhd.com'          => array(
                'shop'=>'yhd',
                'mall'=>'一号店',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/yhd.jpg'
            ),
            'detail.tmall.com'      => array(
                'shop'=>'tmall',
                'mall'=>'天猫',
                'logo'=>'5f7053d9-f14e-4488-9959-6e820c8dd930',
                'mall_logo' => 'http://cdn.11.com/zdm/asset/mall/tmall.jpg'
            ),
            'product.dangdang.com'  => array(
                'shop'=>'dangdang',
                'mall'=>'当当',
                'mall_logo' => 'http://cdn.11.com/zdm/asset/mall/dangdang.jpg'
            ),
            'you.163.com'           => array(
                'shop'=>'you163',
                'mall'=>'网易严选',
                'logo'=>'5efd6f0f-56cb-4eff-b377-a21d083588b8',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/yanxuan.jpg'
            ),
            'www.6pm.com'           => array(
                'shop'=>'6pm',
                'mall'=>'6PM',

            ),
            'www.xiji.com'          => array(
                'shop'=>'xiji',
                'mall'=>'西集网',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/xiji.jpg'

            ),
            'www.ujipin.com'        => array(
                'shop'=>'ujipin',
                'mall'=>'优集品',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/youjipin.jpg'
            ),
            'www.vip.com'           => array(
                'shop'=>'vip',
                'mall'=>'唯品会',

            ),
            'item.secoo.com'         => array(
                'shop'=>'secoo',
                'mall'=>'寺库网',

            ),
            'item.yohobuy.com'       => array(
                'shop'=>'yoho',
                'mall'=>'有货网',

            ),
            'www.yougou.com'        => array(
                'shop'=>'yougou',
                'mall'=>'优购',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/yougou.jpg'
            ),
            'www.okbuy.com'         => array(
                'shop'=>'okbuy',
                'mall'=>'好乐买'
            ),
            'www.mei.com'           => array(
                'shop'=>'mei',
                'mall'=>'魅力惠'
            ),
            'www.fengqu.com'        => array(
                'shop'=>'fengqu',
                'mall'=>'丰趣网'
            ),
            'www.wiggle.com'        => array(
                'shop'=>'wiggle',
                'mall'=>'威骑'
            ),
            'www.wiggle.cn'        => array(
                'shop'=>'wiggle',
                'mall'=>'威骑中国'
            ),
            'detail.tmall.hk'        => array(
                'shop'=>'tmall-hk',
                'mall'=>'天猫香港',
                'mall_logo' => 'http://cdn.111.com/zdm/asset/mall/tmall.jpg'
            ),
            'www.111.com'        => array(
                'shop'=>'111',
                'mall'=>'111',
                'logo'=>'905a7bac-efd5-4e29-bfe3-349a384700d3'
            ),
            'detail.youzan.com'        => array(
	            'shop'=>'youzan',
	            'mall'=>'有赞',
	            'logo'=>'76480c78-e771-4563-96d6-eda91fd0a471'
            ),
        );
        return isset($url_arr[$domain])?$url_arr[$domain]:array();
    }

    //更改图片的alt和title
	//先将对应的字段拿出来重新组合
	public function editImgAlt($str, $alt, $title=""){

		$title  = $title ? $title : $alt;

		$title  = str_replace(" ","",$title);
		$alt    = str_replace(" ","",$alt);

		$preg   = '/<img([^>]+)>/';
		$preg1  = '/title=(\'|\")(.*?)\\1/';
		$preg2  = '/alt=(\'|\")(.*?)\\1/';

		if(preg_match_all($preg, $str, $a)){
			$drr = $a[0];
			foreach ($drr as $k=>$v){
				if(preg_match($preg1, $v, $b)){
					$v      = preg_replace($preg1, 'title="'.$alt.'"',$v);
				}

				if(preg_match($preg2, $v, $b)){
					$v      = preg_replace($preg2, 'alt="'.$alt.'"',$v);
				}

				if(preg_match($preg1, $v, $b) && !preg_match($preg2, $v, $b)){
					//title存在  alt不存在
					$v = preg_replace($preg, '<img alt="'.$alt.'" $1>',$v);
				}

				if(!preg_match($preg1, $v, $b) && preg_match($preg2, $v, $b)){
					// alt存在  title不存在
					$v = preg_replace($preg, '<img title="'.$title.'" $1>',$v);
				}

				if(!preg_match($preg1, $v, $b) && !preg_match($preg2, $v, $b)){
					//title  alt不存在
					$v = preg_replace($preg, '<img alt="'.$alt.'" title="'.$title.'" $1>',$v);
				}

				$drr[$k] = $v;
			}
            $str1 = str_replace($a[0], $drr, $str);
		}
		return $str1 ? $str1 : $str;
	}

	//去掉代码中的html标签
	public function filterHtml($str, $html=""){
		return strip_tags($str, $html);
	}

	public function countdown($unix_timestamp, $is_timestamp=0) {
		$date = $unix_timestamp-time();
		if($is_timestamp){
			$date = $unix_timestamp;
		}
		$day = $date/60/60/24;
		$days = (int)$day;
		$hour = $date/60/60 - $days*24;
		$hours = (int)$hour;
		$minute = $date/60 - $days*24*60 - $hours*60;
		$minutes = (int)$minute;
		$second = $date - $days*24*60*60 - $hours*60*60 - $minutes*60;
		$seconds = (int)$second;
		//$result = array($days,$hours,$minutes,$seconds);
		if($days>0){
			$result = $days.'天';
		}elseif ($hours > 0){
			$result = $hours.'小时';
		}else{
			$result = $minutes.'分钟';
		}

		return $result;
	}

	//获取ip地址
	public function getIp(){
		return Yii::app()->request->getUserHostAddress();
	}

	//匹配文本加粗
	public function titlePreg($string,$arr){

		//$arr    = array('/三星/','/函数/','/使用/');
		//$string = "三星函数使用";
		$string = preg_replace_callback($arr,function($match){
			return '<font class="Z-red">'.$match[0].'</font>';;
		},$string);
		return 	$string;
	}

	/**
	 * 支持优酷、土豆、腾讯视频html到swf转换
	 */
	function convert_html_to_swf($url = '', $type='qq')
	{
		if(!is_string($url) || empty($url)) return ;
		if(strpos($url, 'swf')) return $url;
		preg_match_all('/http:\/\/(.*?)?\.(.*?)?\.com\/(.*)/', $url, $types);
		//$type = $types[2][0];
		$domain = $types[1][0];

		switch ($type) {
			case 'youku' :
				preg_match_all('/http:\/\/v\.youku\.com\/v_show\/id_(.*)?\.html/', $url, $url_array);
				$swf = 'http://player.youku.com/player.php/sid/' . str_replace('/', '', $url_array[1][0]) . '/v.swf';
				break;
			case 'tudou' :
				$method = substr($types[3][0], 0, 1);
				$method = $method == 'p' ? 'v' : $method;
				preg_match_all('/http:\/\/www.tudou\.com\/(.*)?\/(.*)?/', $url, $url_array);
				$str_arr = explode('/', $url_array[1][0]);
				$count = count($str_arr);
				if ($count == 1) {
					$id = explode('.', $url_array[2][0]);
					$id = $id[0];
				} else if ($count == 2) {
					$id = $str_arr[1];
				} else if ($count == 3) {
					$id = $str_arr[2];
				}
				$swf = 'http://www.tudou.com/' . $method . '/' . $id . '/v.swf';
				break;
			case 'qq' :
				$url_array = parse_url($url);
				$swf = "http://static.video.qq.com/TPout.swf?{$url_array['query']}&auto=0";
				break;
			default :
				$swf = $url;
				break;
		}

		return $swf;
	}

	//格式化图片
	public function formatImg($data){
		if(is_array($data)){
			foreach ($data as $k=>$v){
				if(preg_match('/^\S{8}/', $v)){
					$data[$k] = "http://s1.111.com/{$v}/logo";
				}
			}
			return $data;
		}else if(is_string($data)){
			if(preg_match('/^[a-zA-Z0-9]{8}/', $data, $d)){
				return true;
			}
		}

		return false;

	}

    //判断手机号是否合法
    public function checkTel($tel){
        if ( ! $tel ) {
            return array(
                'status' => -1,
                'msg'    => '请输入手机号'
            );
        }

        //判断手机号是否正确
        if ( $tel != "" ) {
            if ( ! preg_match( '/^1([0-9]{10})$/', $tel ) ) {
                return array(
                    'status' => -2,
                    'msg'    => '请填入正确的手机号'
                );
            }
        }

        return array(
            'status' => 1,
            'msg'    => ''
        );
    }

	public function my_http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_AUTOREFERER, true);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	//Yii 设置缓存
	public function setYiiCache($key, $data, $time=60){
		Yii::app()->filecache->set($key, $data, $time);
	}

	//Yii 设置缓存键值
	public function setYiiKey($str){
		return $str ? md5($str) : "";
	}

	//转义字符中的单双引号
	public function escapStr($str){
		return str_replace(array('"', "'"), array('\\"', "\\'"),$str);
	}

	//搜索字符串处理 a,b
	public function searchStr($str, $char1=','){
		$str1 = explode($char1, $str);
		$str2 = implode('"'.$char1.'"', $str1);
		return '"'.$str2.'"';;
	}

	//Yii 获取缓存
	public function getYiiCache($key){
		return Yii::app()->filecache->get($key);
	}

	//Yii 清空缓存
	public function clearYiiCache($key){
		Yii::app()->filecache->delete($key);
	}

	//获取Yii请求参数
	public function getYiiParam($key){
		return Yii::app()->request->getParam($key);
	}


	//获取Yii请求参数
	public function getYiiFilterParam($key){
		return trim($this->filterStrings(Yii::app()->request->getParam($key)));
	}

	//设置redis key
	public function getRedisKey($id, $type){
		return $type.'_num_'.$id;
	}


	public function http_post( $url, $post_data ) {

		$post_data = json_encode( $post_data );
		$curl      = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );

		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );

		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$res = curl_exec( $curl );
		curl_close( $curl );

		return $res;
	}

	//将手机号变成 176****3052
	public function editTelFormat($tel){
		return substr($tel, 0, 3) . "****" . substr($tel, 7);
	}

	//设置支付订单号 $pay_orderid = $common->getPayOrderid($order->orderid, $order->mid, $order->eventid);
	public function getPayOrderid($orderid, $mid, $cid){
		$mid = $mid ? intval($mid) : 0;
		$cid = $cid ? intval($cid) : 0;
		return $orderid. '_' . $mid . '_'. $cid;
	}

	//频率限制
	public function fLimit($func,$uid="", $ip="", $time=5){
		$key = md5("zdm_flimit_".$func.$uid.$ip);

		$data = $this->getYiiCache($key);

		if($data && ($uid || $ip)){
			// 拦截
			$this->error("操作频繁，请稍后再试");
		}
		if($key){
			//设置缓存
			$this->setYiiCache($key, 1, $time);
		}

	}

    //图片filed转换
    static function TransFileid($fileid){
        $fileid = explode('_',$fileid);
        return $fileid[0];
    }

	/**
	 * 接口访问限制
	 * @param int $uid 用户id
	 * @param string $func 用户访问函数
	 * @param int $all_num 规定时间内允许访问次数
	 * @param int $cache_time 规定时间单位秒
	 */
	public function interfaceLimit($uid, $func = "", $all_num = 3, $cache_time = 60){
		// 是否超过评论次数
		$limit_key = 'Common_interfaceLimit_'.$func.'_'.$uid;
		$flimit     = $this->getYiiCache($limit_key);
		if(!$flimit) {

			$flimit = ($all_num-1).','.(time()+$cache_time);
		} else {
			list($num,$end_time) = explode(',',$flimit);

			// 是否过期，如过期重置次数
			$num      = $end_time>time() ? $num      : $all_num;
			$end_time = $end_time>time() ? $end_time : time()+$cache_time;

			// 次数超出
			if((int)$num<1) $this->error("您操作过于频繁,请稍后再试~");

			$flimit = ($num-1).','.$end_time;
		}
		$this->setYiiCache($limit_key,$flimit,86400);
	}

}