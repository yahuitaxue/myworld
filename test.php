<?php
header('content-type:text/html;charset=utf-8');
/*    echo intval((0.1+0.7) * 10 );
    echo PHP_EOL;
    $array = array('a','b','c');
    echo PHP_EOL;
    echo '<hr/>';
    $a = array('a','b','c');
    foreach($a as $k => $v){
        $v = &$a[$k];
    }
    var_dump($_ENV);
    try{
        $throw = new Exception('asdfasf');
        var_dump($throw);
        throw $throw;
    }catch(Exception $re){
        var_dump($re->getMessage());
    }
    echo "<hr/>";
//    $genres = id3_get_genre_list();
//    print_r($genres);
//    $load = sys_getloadavt();
//var_dump($load);

    echo "<hr/>";
    $str = "/[-]?[1-9]{1}\d*(\.\d{1,2})?/";
    $mar = "aaa-123.586asdf";
    preg_match_all($str,$mar,$arr);
    echo "<pre>";
    print_r($arr);

    session_start();
    
    $old_sessionid = session_id();

    session_regenerate_id();

    $new_sessionid = session_id();

    echo "Old Session: $old_sessionid<br />";
    echo "New Session: $new_sessionid<br />";

    print_r($_SESSION);

    echo "<hr/>";
    echo addcslashes('asdf[];./','z.A');

    echo "<hr/>";
    echo convert_uudecode("+22!L;W9E(%!(4\"$`\n`");

    echo "<hr/>";
    $checksum = crc32("The quick brown fox jumped over the lazy dog.");
    printf("%u\n", $checksum);

    echo "<hr/>";
    echo md5('abc',true);

    echo "<hr/>";
    $str = "Hello world. (can you hear me?)";
    echo quotemeta($str);

    echo "<hr/>";
    echo strlen(sha1_file('./test.php'));

    echo "<hr/>";
    $keys = array('foo', 5, 10, 'bar');
    $values = array('a','b','c','d');
    $a = array_fill_keys($keys, $values);
    print_r($a);

    echo "<hr/>";
    print_r(array(-2=>'a',-1=>'b',0=>'c',1=>'d'));*/

//    header("Content-type: image/png");
//
//    //创建画布，返回一个资源类型的变量$image，并在内存中开辟一个临时区域
//    $image = imagecreatetruecolor(100, 100);                //创建画布大小为100x100
//
//    //设置图像中所需的颜色，相当于在画画时准备的染料盒
//    $white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);          //为图像分配颜色为白色
//    $gray = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);           //为图像分配颜色为灰色
//    $darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);       //为图像分配颜色为暗灰色
//    $navy = imagecolorallocate($image, 0x00, 0x00, 0x80);           //为图像分配颜色为深蓝色
//    $darknavy = imagecolorallocate($image, 0x00, 0x00, 0x50);       //为图像分配颜色为暗深蓝色
//    $red = imagecolorallocate($image, 0xFF, 0x00, 0x00);           //为图像分配颜色为红色
//    $darkred = imagecolorallocate($image, 0x90, 0x00, 0x00);       //为图像分配颜色为暗红色
//
//    imagefill($image, 0, 0, $white);            //为画布背景填充背景颜色
//    //动态制作3D效果
//    for ($i = 60; $i >50; $i--){                //循环10次画出立体效果
//        imagefilledarc($image, 50, $i, 100, 50, -160, 40, $darknavy, IMG_ARC_PIE);
//        imagefilledarc($image, 50, $i, 100, 50, 40, 75, $darkgray, IMG_ARC_PIE);
//        imagefilledarc($image, 50, $i, 100, 50, 75, 200, $darkred, IMG_ARC_PIE);
//    }
//
//    imagefilledarc($image, 50, 50, 100, 50, -160, 40, $navy, IMG_ARC_PIE);      //画一椭圆弧且填充
//    imagefilledarc($image, 50, 50, 100, 50, 40 , 75, $gray, IMG_ARC_PIE);      //画一椭圆弧且填充
//    imagefilledarc($image, 50, 50, 100, 50, 75, 200, $red, IMG_ARC_PIE);      //画一椭圆弧且填充
//
//    imagestring($image, 1, 15, 55, '34.7%', $white);                //水平地画一行字符串
//    imagestring($image, 1, 45, 35, '55.5%', $white);                //水平地画一行字符串
//
//    //向浏览器中输出一个GIF格式的图片
//    header('Content-type:image/png');               //使用头函数告诉浏览器以图像方式处理以下输出
//    imagepng($image);                       //向浏览器输出
//    imagedestroy($image);                   //销毁图像释放资源
//    $file = fopen('./test.png','r');
//    $bin = fread($file, 2); //只读2字节
//    fclose($file);
//    $strInfo = @unpack("C2chars", $bin);
//    var_dump($strInfo);
//
//    echo date('Y m d H:i:s',time());

// var_dump(getimagesize('./test.png'));

// $ii = 11;
// $a = 56;
// function test(&$a){
//     global $ii;
//     $ii = &$a;
//     $ii++;
// }
// var_dump($ii);
// test($a);
// var_dump($ii);
//    echo 3214587965.4555555555555962478225646321534646453;

//    $a = array(1,2,3);
//        var_dump(current($a));
//        // $b = &$a;
//        $b = $a;
//    foreach ($a as $key => $value) {
//        var_dump(current($a));
//    }

//    echo "<hr/>";
//    $obj = new mysqli('47.94.82.246','root','EKNdnk543kdD','goldkgui','3306');
//    $result = $obj->multi_query('select * from ecs_users; select * from ecs_users;');
//    $aa = $obj->store_result();
//    var_dump($aa->fetch_all(MYSQLI_ASSOC));
//    $obj->next_result();
//    $aa = $obj->store_result();
//    var_dump($aa->fetch_all(MYSQLI_ASSOC));

//$pdo = new PDO('mysql:host=localhost;dbname=shop', 'root', 'root');
//$sql = 'SELECT * FROM ecs_ad WHERE ad_id < ? LIMIT 10';
//$stm = $pdo->prepare($sql);
//$id = 10;
//$stm->bindParam(1, $id);
//$stm->execute();
//$res = $stm->rowCount();
//$res = $stm->fetchAll(PDO::FETCH_ASSOC);
//var_dump($res);
//
//$pdo = new PDO('mysql:host=localhost;dbname=shop','root','root');
//$sql = 'SELECT * FROM ecs_ad WHERE ad_id BETWEEN :st AND :en';
//$stm = $pdo->prepare($sql);
//$stm -> bindValue(':st',10);
//$stm -> bindValue(':en',30);
//$stm -> execute();
//$res = $stm -> fetchAll(PDO::FETCH_ASSOC);
//echo $stm -> rowCount();
//var_dump($res);

define('PDO_HOST','localhost');
define('PDO_DBNAME','test');
define('PDO_NAME','root');
define('PDO_PASSWORD','root');
define('PDO_PORT','3306');
define('PDO_TYPE','mysql');
define('DB_CHARSET','utf8');


class PdoMysql{
    public static $config = NULL;
    public static $link = NULL;
    public static $pconnect = FALSE;
    public static $isconnect = FALSE;
    public static $statement = NULL;
    public static $lastid = NULL;
    public function __construct($conf=NULL){
        if(!class_exists('PDO')){
            echo '请先安装PDO扩展';
        }
        if(!is_array($conf)){
            $conf = array(
                'host'=>PDO_HOST,
                'dbname'=>PDO_DBNAME,
                'name'=>PDO_NAME,
                'password'=>PDO_PASSWORD,
                'port'=>PDO_PORT,
                'type'=>PDO_TYPE,
                'dsn'=>PDO_TYPE.':host='.PDO_HOST.';dbname='.PDO_DBNAME
            );
        }
        if(empty($conf['host'])){
            echo '主机名为空';
        };
        self::$config = $conf;
        if(empty(self::$config['params'])){
            self::$config['params'] = array();
        }
        if(empty(self::$link)){
            $c = self::$config;
            if(empty(self::$pconnect)){
                $c['params'][constant("PDO::ATTR_PERSISTENT")] = TRUE;
            }
            try{
                self::$link = new PDO($c['dsn'],$c['name'],$c['password'],$c['params']);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
            if(!self::$link){
                echo 'PDO连接错误';
            }
            self::$link->exec('SET NAMES '.DB_CHARSET);
            self::$isconnect = TRUE;
            unset($c);
        }
    }

    public function getALL($sql = NULL){
        if(!empty($sql)){
            self::query($sql);
        }
        $result = self::$statement -> fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOne($sql = NULL){
        if(!empty($sql)){
            self::query($sql);
        }
        $result = self::$statement -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getByPriid($table = NULL, $id = NULL, $fields = NULL){
        if(empty($table) || empty($id)){
            return FALSE;
        }
        if(is_array($fields)){
            foreach($fields as $key => $value){
                $f .= self::showValue($value).'';
            }
        }elseif(empty($fields)){
            $f = '*';
        }else{

        }
        $result = self::$statement -> fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function execute($sql = NULL){
        if(!empty(self::$statement)){
            self::free();
        }
        $result = self::$link->exec($sql);
        self::returnerror($sql);
        if($result){
            self::$lastid = self::$link -> lastInsertId();
            return $result;
        }else{
            return false;
        }
    }

    public static function free(){
        self::$statement = NULL;
    }

    public static function returnerror($sql = NULL){
        $sign = empty(self::$statement) ? self::$link : self::$statement;
        $errorinfo = $sign -> errorInfo();
        if($errorinfo[0] != '00000'){
            echo implode('---',$errorinfo).'---错误的SQL语句：'.$sql;
        }
        if(empty($sql)){
            echo '空的SQL语句';
        }
    }

    public static function query($sql = NULL){
        if(!empty($sql)){
            if(!empty(self::$statement)){
                self::free();
            }
            self::$statement = self::$link -> prepare($sql);
            $res = self::$statement -> execute();
            self::returnerror($sql);
            return $res;
        }
    }
}

$obj = new PdoMysql();
$obj = new PdoMysql();
//$sql = 'SELECT * FROM ecs_account_log LIMIT 1';
//$res = $obj->getALL($sql);
//var_dump($res);
//$res = $obj->getOne($sql);
//var_dump($res);
$sql = 'INSERT INTO info(name,email) VALUES ("哈哈","haha@qq.com")';
$res = $obj ->execute($sql);
echo $obj->lastid;
var_dump($res);
?>
<!--<!doctype html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <title>Document</title>-->
<!--</head>-->
<!--<body>-->
<!--    <form method="post" action="./test.php" enctype="multipart/form-data">-->
<!--        <input type="file" name="file"/>-->
<!--        <input type="submit" />-->
<!--    </form>-->
<!--</body>-->
<!--</html>-->
<!--<script type="text/javascript">-->
<!--    document.write('<hr/>');-->
<!--    var str="aaa-123.586asdf"-->
<!--    document.write(str.match(/[-]?[1-9]{1}\d*(\.\d{1,2})?/)[0]);-->
<!--</script>-->
