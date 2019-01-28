<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * GDクラス
 *
 * 画像ライブラリGDを取り扱うクラスです。
 *
 * @Singleton
 */
class BEAR_Img_Adapter_GD extends BEAR_Img_Adapter
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * イメージアトリビュート
     *
     * @var string
     */
    protected $_srcAttr;

    /**
     * GDイメージリソース
     *
     * @var resource
     */
    private $_imgResource;

    /**
     * 元イメージタイプ
     *
     * @var string
     */
    private $_srcType;

    /**
     * 新イメージ幅
     *
     * @var int
     */
    private $_newWidth;

    /**
     * 新イメージ幅
     *
     * @var int
     */
    private $_newHeight;

    /**
     * Constructor
     *
     * @throws BEAR_Img_Adapter_GD_Exception
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        if (! function_exists('gd_info')) {
            throw $this->_exception('GD extention is not loaded');
        }
    }

    /**
     * ファイルのロード
     *
     * <pre>
     * $fileにはローカルファイルのパスまたはリモートファイルのURLを指定します。
     * リモートファイルの読み込みにはphp.iniでallow_url_fopen =Onの設定が必要です。
     * </pre>
     *
     * @param string $file   ファイルパス
     * @param string $format ファイルフォーマット
     *
     * @throws BEAR_Img_Adapter_GD_Exception
     */
    public function load($file, $format = '')
    {
        assert(is_string($file));
        ini_set('user_agent', BEAR_Img::UA);
        $this->file = $file;
        if ($format) {
            $this->format = strtolower($format);
        } else {
            //check if gif
            if (stristr(strtolower($this->file), '.gif')) {
                $this->format = 'gif';
            } elseif (stristr(strtolower($this->file), '.jpg') || stristr(strtolower($this->file), '.jpeg')) {
                $this->format = 'jpeg';
            } elseif (stristr(strtolower($this->file), '.png')) {
                $this->format = 'png';
            //unknown file format
            } else {
                $info = compact($file, $format);

                throw $this->_exception(
                    'Unknown file format',
                    ['info' => $info]
                );
            }
        }
        // イメージリソース作成
        assert(is_string($this->format));
        switch ($this->format) {
            case 'gif':
                $this->_imgResource = imagecreatefromgif($this->file);

                break;
            case 'jpeg':
            case 'jpg':
                $this->_imgResource = imagecreatefromjpeg($this->file);

                break;
            case 'png':
                $this->_imgResource = imagecreatefrompng($this->file);

                break;
            default:
                $info = ['format' => $this->format];

                throw $this->_exception('load format error', compact('info'));
        }
        list($width, $height, $type, $attr) = $info = getimagesize($this->file);
        if (! $info) {
            $this->_log->log('IMG load error', $file);
            $info = ['file' => $file];

            throw $this->_exception('Image Load Error', ['info' => $info]);
        }
        $this->_srcWidth = $width;
        $this->_srcHeight = $height;
        $this->_srcType = $type;
        $this->_srcAttr = $attr;
        $log = [];
        $log['load'] = ['load' => $file, 'format' => $format];
        $log['result'] = ['info' => $info, 'rsc' => (string) $this->_imgResource];
        $this->_log->log('IMG load', $log);
    }

    /**
     * 画像のリサイズ
     *
     * <pre>
     * 画像を横幅に合わせてリサイズします。幅、高さ、の順で
     * 指定が優先されます。
     * $smallOnlyをtrueにすると画像拡大をしません。
     * サムネール画像作成などにつかいます。
     * </pre>
     *
     * @param bool|int $width     幅
     * @param bool|int $height    高さ
     * @param bool     $smallOnly 縮小のみ（小さい画像を大きくはしない）
     */
    public function resize($width = false, $height = false, $smallOnly = false)
    {
        // 画像が小さいときは処理しない
        if ($smallOnly && ($width && $width > $this->_srcWidth) || ($height && $height > $this->_srcHeight)) {
            return;
        }
        //大きさの変化がないときあるいは指定が無いときはなにもしない
        if (! $width && ! $height || $this->_srcWidth == $width && $this->_srcHeight == $height) {
            return;
        }
        if ($width / $this->_srcWidth > $height / $this->_srcHeight) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $magnify = $height / $this->_srcHeight;
        } else {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $magnify = $width / $this->_srcWidth;
        }
        if ($width / $this->_srcWidth > $height / $this->_srcHeight) {
            $magnify = $height / $this->_srcHeight;
        } else {
            $magnify = $width / $this->_srcWidth;
        }
        $newWidth = $magnify * $this->_srcWidth;
        $newHeight = $magnify * $this->_srcHeight;
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled(
            $newImage,
            $this->_imgResource,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $this->_srcWidth,
            $this->_srcHeight
        );
        $this->_imgResource = $newImage;
        $this->_newWidth = $newWidth;
        $this->_newHeight = $newHeight;
    }

    /**
     * 画像表示
     *
     * ヘッダーと画像をhttp出力します。
     *
     * @param bool|string $format 画像ファイルの場所(URL or fileパス)
     */
    public function show($format = false)
    {
        // clean buffer
        ob_clean();
        if (! $format) {
            $format = $this->format;
        } else {
            $format = strtolower($format);
        }
        // clean buffer
        ob_clean();
        switch ($format) {
            case 'gif':
                $this->result = imagegif($this->_imgResource);

                break;
            case 'jpeg':
                $this->result = imagejpeg($this->_imgResource);

                break;
            case 'png':
                $this->result = imagepng($this->_imgResource);

                break;
            default:
                trigger_error('format error', $format, E_USER_ERROR);
        }
        $this->_log->log(
            'IMG show',
            [
                'format' => $format,
                'rsc' => (string) $this->_imgResource,
                'result' => $this->result
            ]
        );
        parent::header($format);
    }

    /**
     * 画像保存
     *
     * <pre>
     * 指定のフォーマットで画像をファイル保存します。
     * $isTemporaryがtrueの時は、ページ表示終了時にファイルを消去します。
     * テンポラリーファイルの保存のときに使用します。
     * </pre>
     *
     * @param string $filePath 保存画像のファイルパス
     * @param string $format   画像ファイルのフォーマット
     *
     * @throws BEAR_Img_Adapter_GD_Exception
     */
    public function save($filePath, $format)
    {
        switch ($format) {
            case 'gif':
                $result = imagegif($this->_imgResource, $filePath);

                break;
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($this->_imgResource, $filePath);
                $format = 'jpeg';

                break;
            case 'png':
                $result = imagepng($this->_imgResource, $filePath);

                break;
            default:
                $info = compact('formart');

                throw $this->_exception(
                    'save formart error',
                    [
                        'info' => $info
                    ]
                );
        }
        $this->_log->log(
            'IMG saved',
            [
                'format' => $format,
                'file' => $filePath,
                'rsc' => (string) $this->_imgResource,
                'result' => $result
            ]
        );
    }
}
