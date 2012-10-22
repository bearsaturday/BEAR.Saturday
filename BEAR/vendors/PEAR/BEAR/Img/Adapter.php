<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Img
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 1251 2009-12-07 08:01:31Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Img/BEAR_Img.html
 */
/**
 * イメージアダプタークラス
 *
 * <pre>
 * イメージ抽象クラスです。BEAR/Img/Adapter/の各クラスで実装します。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Img
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Adapter.php 1251 2009-12-07 08:01:31Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Img/BEAR_Img.html
 * @abstract
 *
 */
abstract class BEAR_Img_Adapter extends BEAR_Base
{

    /**
     * イメージリソース
     *
     * @var resource
     */
    public $image;

    /**
     * 元画像の幅
     *
     * @var integer
     */
    protected $_srcWidth;

    /**
     * 元画像の高さ
     *
     * @var integer
     */
    protected $_srcHeight;

    /**
     * 元画像の属性
     *
     * @var string
     */
    protected $_srcAttr;

    /**
     * 画像のタイプ
     *
     * <pre>
     * IMAGETYPE_GIF | IMAGETYPE_JPEG | IMAGETYPE_PNG
     * </pre>
     *
     * @var string
     */
    public $type;

    /**
     * デストラクタで消去するファイルリスト
     *
     * @var array
     */
    static $deleteFiles = array();

    /**
     * 画像ライブラリオブジェクト
     *
     * @var GD | iMagick | Cariro
     */
    public $adapter;

    /**
     * 出力結果
     *
     * @var bool
     */
    protected $_result;

    /**
     * ファイルの消去
     *
     * 作業用のファイルを消去リストに追加します。
     *
     * @param string $file ファイル
     *
     * @return void
     */
    public function deleteFile($file)
    {
        static $cnt = 0;
        BEAR_Img::$deleteFiles[$cnt] = $file;
        $cnt++;
    }

    /**
     * 画像情報の取得
     *
     * <pre>
     * getimagesizeで得られる画像情報を以下のプロパティに格納します。
     *
     * _srcWidth int
     * srcHeight int
     * srcType   int
     * srcAttr   string
     * </pre>
     *
     * @return void
     */
    protected function getImageInfo()
    {
        list($width, $height, $type, $attr) = getimagesize($this->file);
        // src
        $this->_srcWidth = $width;
        $this->srcHeight = $height;
        $this->srcType = $type;
        $this->srcAttr = $attr;
    }

    /**
     * ヘッダー出力
     *
     * @param int $format フォーマット
     * @param int $expire expire
     *
     * @return void
     */
    protected function header($format = false, $expire = 0)
    {
        if ($format) {
            $mimeType = 'image/' . strtolower($format);
        } else {
            $mimeType = image_type_to_mime_type($this->srcType);
        }
        header("Content-type: " . $mimeType);
        //        header("Content-Type: image/gif");
        $exp = gmdate('D, d M Y H:i:s', time() + $expire) . ' GMT';
        header("Expires: " . $exp);
        header("Last-Modified: " . gmdate('D, d M Y H:i:s ', time()) . ' GMT');
        header("Cache-Control: public");
        header("Pragma: ");
    }

    /**
     * 一時ファイル名を取得
     *
     * <pre>一時画像ファイル名を生成します。
     * $deleteオプションがtrueの場合、デストラクタでテンポラリーファイルは消去されます
     * </pre>
     *
     * @param string $file   ファイル名
     * @param string $delete 消去
     *
     * @return string
     */
    public function getTmpFileName($file = false, $delete = true)
    {
        $file = ($file) ? $file : uniqid();
        // ファイル名生成
        $filePath = _BEAR_APP_HOME . BEAR_Img::TMP_DIR . '/img-' . md5($file) . '.png';
        // 削除ファイルとしてマーク
        if ($delete) {
            $this->deleteFile($filePath);
        }
        return $filePath;
    }

    /**
     * モバイル端末に合わせた画像の最大リサイズ
     *
     * @return void
     */
    public function resizeMobile()
    {
        $agent = BEAR::dependency('BEAR_Agent');
        /* @var $agent BEAR_Agent */
        $display = BEAR::dependency('BEAR_Agent')->agentMobile->getDisplay();
        list($width, $hight) = $display->getSize();
        if ($width == 0) {
            //サイズが取れないときはQVGA
            $width = 240;
            $hight = 320;
        }
        $this->resize($width, $hight, true);
    }

    /**
     * ファイルの読み込み
     *
     * <pre>
     * ローカル・リモートファイルにかかわらずファイルを読み込みます。
     * リモートファイルの場合はローカルにテンポラリーファイルが
     * 作成されその名前が返されます。作られたテンポラリーファイルは
     * デストラクタで消去されます。キャッシュはされません。
     * </pre>
     *
     * @param string $file ファイル名
     *
     * @return string
     */
    public function loadRemoteFile($file)
    {
        if (strpos($file, 'http') !== false) {
            $tmpFile = $this->getTmpFileName($file);
            if (!file_exists($tmpFile)) {
                //リモートファイルの取得
                $remoteFile = file_get_contents($file);
                if ($remoteFile === false) {
                    $this->_error("loadRemoteFile file=[{$remoteFile}]");
                }
                file_put_contents($tmpFile, $remoteFile);
                BEAR_Img::$deleteFiles[] = $tmpFile;
            }
            $file = $tmpFile;
        }
        return $file;
    }

    /**
     * エラー終了
     *
     * @param string $errorFunc ファンクション名
     *
     * @return void
     */
    protected function _error($errorFunc)
    {
        //エラーヘッダー
        header('HTTP/1.0 503 Service Temporarily Unavailable.');
        $isRes = (is_resource($this->image)) ? 'true' : 'false';
        if ($this->_config['debug']) {
            $errMsg = "BEAR_Img error! func=[{$errorFunc}] is_resource=[{$isRes}] ";
            header("x-imgcairo-error: {$errMsg}");
            trigger_error($errMsg, E_USER_WARNING);
        } else {
        }
        exit();
    }

    /**
     * image typeから拡張子を求める
     *
     * @param string
     */
    public function getExtention($imageType)
    {
        switch ($imageType) {
        case 'image/bmp' :
            return 'bmp';
        case 'image/gif' :
            return 'gif';
        case 'image/jpeg' :
            return 'jpg';
        case 'image/tiff' :
            return 'tif';
        case 'image/png' :
            return 'png';
        default :
            return null;
        }
    }
}
