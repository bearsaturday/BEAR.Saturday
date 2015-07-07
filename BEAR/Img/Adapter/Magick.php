<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category   BEAR
 * @package    BEAR_Img
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Magick.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 */
/**
 * iMagickクラス
 *
 * <pre>
 * Image MagicおよびGraphich MagickをサポートしたPECLのiMagickをサポートするクラスです
 * </pre>
 *
 * Example. 画像のリサイズ表示
 *</pre>
 * <code>
 *  $img = BEAR_Img::getInstance(BEAR_Img::ADAPTER_MAGICK);
 *  $img->load('http://www.bear-project.net/images/eye.jpg');
 *  $img->resize(320,240);
 *  $img->show('jpeg');
 * </code>
 *
 * <pre>
 * Example. iMagickオブジェクトを直接操作
 *</pre>
 * <code>
 *  $img = BEAR_Img::getInstance(BEAR_Img::ADAPTER_MAGICK);
 *  $img->load('http://www.bear-project.net/images/eye.jpg');
 *  $img->resize(320,240);
 *  // 木炭画エフェクト
 *  $img->adapter->charcoalImage(1, 1);
 *  $img->save(_BEAR_APP_HOME . '/tmp/picure.jpeg');
 * </code>
 *
 * @category   BEAR
 * @package    BEAR_Img
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Magick.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       http://www.bear-project.net/
 *
 * @Singleton
 */
class BEAR_Img_Adapter_Magick extends BEAR_Img_Adapter
{
    /**
     * アニメGIFではない
     *
     * @var bool
     */
    protected $_isAnimGif = false;

    /**
     * アニメGIFファイル名
     *
     * @var bool
     */
    protected $_animGifFile = '';

    /**
     * Constructor
     *
     */
    /**
     * Constructor
     *
     * @param array $config
     *
     * @throws BEAR_Img_Adapter_Magick_Exception
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        //インストールチェック
        if (!class_exists('Imagick')) {
            throw $this->_exception('iMagick extention is not loaded');
        }
        $this->adapter = new Imagick();
    }

    /**
     * ファイルのロード
     *
     * <pre>$fileにはローカルファイルのパスまたはリモートファイルのURLを指定します。
     * リモートファイルの読み込みにはphp.iniでallow_url_fopen =Onの設定が必要です。</pre>
     *
     * @param string $file
     *
     * @return void
     * @throws BEAR_Img_Adapter_Magick_Exception
     */
    public function load($file)
    {
        assert(is_string($file));
        ini_set('user_agent', BEAR_Img::UA);
        $this->file = $file;
        if (strpos($file, 'http') !== false) {
            //リモートファイルの取得
            $remoteFile = file_get_contents($file);
            if (!$remoteFile) {
                $this->_thisError('load', "file_get_contents file is [{$file}]");
            }
            $file = $this->getTmpFileName();
            $result = file_put_contents($file, $remoteFile);
            if ($result === false) {
                $msg = 'file_put_contents error.（ファイル作成ができません)';
                $info = array('$file' => $file);
                throw $this->_exception($msg, array('info' => $info));
            }
        }
        //ファイルサイズのチェック
        $fileSize = filesize($file);
        if ($fileSize == 0) {
            $this->_thisError('load', "filze size is zero.file is [{$file}]");
        }
        /* @var $this->adapter Imagick */
        $this->image = $this->adapter->readImage($file);
        if ($this->image === false) {
            $this->_thisError('load', "imagick_readimage file is [{$file}]");
        }
        $this->getImageInfo();
        //アニメGIF
        if ($this->adapter->getNumberImages() >= 2) {
            $this->_isAnimGif = true;
            $this->_animGifFile = $file;
        }
    }

    /**
     * ヘッダーを出力
     *
     * イメージリソースの内容をみてHTTPヘッダーを出力します。
     * 他の画像タイプと違い画像タイプを出力する必要がありません
     * imagick_getmimetypeを使用
     *
     * @param null $format
     * @param null $expire
     */
    public function header($format = null, $expire = null)
    {
        $linenum = $filename = '';
        if (headers_sent($filename, $linenum)) {
            $msg = "header is send in [$filename] , line [{$linenum}]";
            $this->_thisError('header', $msg);
        }
        if ($this->_isAnimGif) {
            header("Content-type: image/gif");
        } else {
            $mimeType = strtolower($this->adapter->getImageFormat());
            if ($mimeType) {
                header("X-Content-type: image/" . $mimeType);
                header("Content-type: image/" . $mimeType);
            } else {
                $this->_thisError('header', "image type=[{$mimeType}]");
            }
        }
    }

    /**
     * 画像のリサイズ
     *
     * <pre>
     * 画像をリサイズします。アニメーションGIFもリサイズできます。
     * リサイズ時に$filterを指定することもできます。
     * $filterはIMAGICK_FILTER_*形式で指定します。
     * </pre>
     *
     * @param int  $width
     * @param int  $height
     * @param int  $filter
     * @param int  $blur
     * @param bool $fit
     *
     * @return void
     *
     * @see http://jp2.php.net/manual/ja/ref.imagick.php
     */
    public function resize($width = 240, $height = 240, $filter = Imagick::FILTER_LANCZOS, $blur = 1, $fit = true)
    {
        //imagick_getlistsize
        if ($this->_isAnimGif) {
            if ($this->_srcWidth > $width) {
                $this->_resizeAnim($width);
            }
        } else {
            $this->adapter->resizeImage($width, $height, $filter, $blur, $fit);
        }
    }

    /**
     * アニメーションGIFのリサイズ
     *
     * <pre>
     * アニメーションGIFをリサイズします。magic関数には用意されていない機能なので
     * シェルでconvertコマンドを実行しています
     * </pre>
     *
     * @param int $width 画像の幅
     *
     * @return void
     */
    protected function _resizeAnim($width = 240)
    {
        //        convert animt.gif -coalesce -resize 640 -deconstruct resized.gif
        $fromFile = $this->getTmpFileName();
        $this->save($fromFile);
        $toFile = $this->getTmpFileName();
        $command = "convert {$fromFile} -resize {$width} {$toFile}";
        $result = '';
        system($command, $result);
        if ($result) {
            trigger_error("imagemagick convert error result=[$result]", E_USER_WARNING);
        }
        $this->_animGifFile = $toFile;
    }

    /**
     * 画像を指定のフォーマットで表示
     *
     * 画像を表示します。
     *
     * @param string $format 画像フォーマット
     *
     * @return void
     */
    public function show($format)
    {
        // clean buffer
        ob_clean();
        $this->header();
        if ($this->_isAnimGif) {
            readfile($this->_animGifFile);
        } else {
            $this->adapter->setFormat($format);
            $blob = $this->adapter;
            echo $blob;
        }
    }

    /**
     * 画像保存
     *
     * 指定のパスに画像を保存します。
     *
     * @param string $filePath 保存画像のファイルパス
     * @param mixed  $format   フォーマット
     */
    public function save($filePath, $format = false)
    {
        $this->adapter->setFormat($format);
        $result = $this->adapter->writeImage($filePath);
        if (!$result) {
            trigger_error("iMagick: Image file write error [$filePath]", E_USER_ERROR);
        }
    }

    /**
     * エラー終了
     *
     * エラー終了します。
     *
     * @param string $func コール元のメソッド名
     * @param string $msg  エラーメッセージ
     *
     * @return void
     * @throws BEAR_Img_Adapter_Magick_Exception
     */
    private function _thisError($func, $msg = null)
    {
        //エラーヘッダー
        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedFunctionInspection */
        $reason = (is_resource($this->image)) ? imagick_failedreason($this->image) : "no image resource";
        /** @noinspection PhpUndefinedFunctionInspection */
        /** @noinspection PhpUndefinedFunctionInspection */
        $description = (is_resource($this->image)) ? imagick_faileddescription($this->image) : "no image resource";
        $isResource = (is_resource($this->image)) ? 'true' : 'false';
        $msg .= 'iMagcik Error:' . $msg;
        $info = array(
            'func' => $func,
            'isResource' => $isResource,
            'reason' => $reason,
            'description' => $description
        );
        throw $this->_exception($msg, array('info' => $info));
    }
}
