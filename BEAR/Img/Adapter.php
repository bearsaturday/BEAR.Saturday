<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
/**
 * イメージアダプター抽象クラス
 *
 *
 *
 *
 * @abstract
 */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedClassInspection */
abstract class BEAR_Img_Adapter extends BEAR_Base
{
    /**
     * イメージリソース
     *
     * @var resource
     */
    public $image;

    /**
     * デストラクタで消去するファイルリスト
     *
     * @var array
     */
    public static $deleteFiles = array();

    /**
     * 画像ライブラリオブジェクト
     *
     * @var GD | iMagick | Cariro
     */
    public $adapter;
    /**
     * @var string
     */
    protected $file;

    /**
     * 元画像の幅
     *
     * @var int
     */
    protected $_srcWidth;

    /**
     * 元画像の高さ
     *
     * @var int
     */
    protected $_srcHeight;

    /**
     * 元画像の属性
     *
     * @var string
     */
    protected $_srcAttr;

    /**
     * 出力結果
     *
     * @var bool
     */
    protected $_result;

    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * 画像のタイプ
     *
     * <pre>
     * IMAGETYPE_GIF | IMAGETYPE_JPEG | IMAGETYPE_PNG
     * </pre>
     *
     * @var string
     */
    private $_srcType;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Inject
     */
    public function onInject()
    {
        $this->_log = BEAR::dependency('BEAR_Log');
    }

    /**
     * ファイルの消去
     *
     * 作業用のファイルを消去リストに追加します。
     *
     * @param string $file ファイル
     */
    public function deleteFile($file)
    {
        static $cnt = 0;

        BEAR_Img::$deleteFiles[$cnt] = $file;
        $cnt++;
    }

    /**
     * 一時ファイル名を取得
     *
     * 一時画像ファイル名を生成します。
     * $deleteオプションがtrueの場合、デストラクタでテンポラリーファイルは消去されます
     *
     * @param mixed $file   ファイル名
     * @param mixed $delete 消去
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
     */
    public function resizeMobile()
    {
        /* @var $agent BEAR_Agent */
        /** @noinspection PhpUndefinedMethodInspection */
        $display = BEAR::dependency('BEAR_Agent')->agentMobile->getDisplay();
        /* @noinspection PhpUndefinedMethodInspection */
        list($width, $hight) = $display->getSize();
        if ($width == 0) {
            //サイズが取れないときはQVGA
            $width = 240;
            $hight = 320;
        }
        /* @noinspection PhpUndefinedMethodInspection */
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
        if (strpos($file, 'http://') !== 0) {
            return $file;
        }
        $tmpFile = $this->getTmpFileName($file);
        if (! file_exists($tmpFile)) {
            //リモートファイルの取得
            $remoteFile = file_get_contents($file);
            if ($remoteFile === false) {
                $this->_error("loadRemoteFile file=[{$remoteFile}]");
            }
            file_put_contents($tmpFile, $remoteFile);
            BEAR_Img::$deleteFiles[] = $tmpFile;
        }

        return $tmpFile;
    }

    /**
     * image typeから拡張子を求める
     *
     * @param string $imageType
     *
     * @return string
     */
    public function getExtention($imageType)
    {
        switch ($imageType) {
            case 'image/bmp':
                return 'bmp';
            case 'image/gif':
                return 'gif';
            case 'image/jpeg':
                return 'jpg';
            case 'image/tiff':
                return 'tif';
            case 'image/png':
                return 'png';
            default:
                return null;
        }
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
     */
    protected function getImageInfo()
    {
        list($width, $height, $type, $attr) = getimagesize($this->file);
        // src
        $this->_srcWidth = $width;
        $this->_srcHeight = $height;
        $this->_srcType = $type;
        $this->_srcAttr = $attr;
    }

    /**
     * ヘッダー出力
     *
     * @param mixed $format フォーマット
     * @param int   $expire expire
     */
    protected function header($format = false, $expire = 0)
    {
        if ($format) {
            $mimeType = 'image/' . strtolower($format);
        } else {
            $mimeType = image_type_to_mime_type($this->_srcType);
        }
        header('Content-type: ' . $mimeType);
        //        header("Content-Type: image/gif");
        $exp = gmdate('D, d M Y H:i:s', time() + $expire) . ' GMT';
        header('Expires: ' . $exp);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', time()) . ' GMT');
        header('Cache-Control: public');
        header('Pragma: ');
    }

    /**
     * エラー終了
     *
     * @param string $errorFunc ファンクション名
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
        }
        exit();
    }
}
