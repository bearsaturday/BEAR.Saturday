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
 * @version    SVN: Release: @package_version@ $Id: Cairo.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 */

/**
 * Cairoクラス
 *
 * <pre>
 * PECLのCairo Wrapperをサポートするクラスです
 *
 * Example 1. 画像のリサイズ表示
 * </pre>
 *
 * <code>
 *  $img = BEAR_Img::getInstance(BEAR_Img::Magick);
 *  $img->load(LOCAL_IMG_FILE);
 *  //$img->load(REOMOTE_IMG_FILE);         //http://ではじまるリモートファイルも可
 *  $img->resize(30);
 *  $img->show
 * </code>
 *
 * Example 2. 画像とテキストを合成してiMagickを使用してJPEG表示
 *
 * <code>
 *       $img = BEAR_Img::getInstance(BEAR_Img::ADAPTER_MAGICK);
 *       $file = _BEAR_APP_HOME . '/htdocs/eye.png';
 *       $img->load($file);
 *       $img = BEAR_Img::changeInstance(BEAR_Img::ADAPTER_CAIRO);
 *       $img->addImage('http://www.christmastail.com/picbbs/icon/016.png', 50, 50);
 *       $img->addText('フェリクス星雲NGC7293, 通称「神の目」', 0, 80, 24,
 *         BEAR_Img::CENTER, array(200, 200, 200), array(100,128,128),
 *         'Hiragino Mincho ProN'); $img->resize();
 * </code>
 *
 * @category   BEAR
 * @package    BEAR_Img
 * @subpackage Adapter
 * @author     Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright  2008-2015 Akihito Koriyama  All rights reserved.
 * @license    http://opensource.org/licenses/bsd-license.php BSD
 * @version    SVN: Release: @package_version@ $Id: Cairo.php 2486 2011-06-06 07:44:05Z akihito.koriyama@gmail.com $
 * @link       https://github.com/bearsaturday
 *
 * @Singleton
 */
class BEAR_Img_Adapter_Cairo extends BEAR_Img_Adapter
{
    /**
     * Cairoサーフェイス
     *
     * @var resource
     */
    public $surface;

    protected $file;

    /**
     * @var BEAR_Log
     */
    protected $_log;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        //インストールチェック
        if (!function_exists("cairo_create")) {
            trigger_error('Error: You need Cairo Library', E_ERROR);
            exit();
        }
    }

    /**
     * フォント情報
     *
     * @var array
     */
    private $_fontInfo;

    /**
     * ファイルのロード
     *
     * <pre>
     * $fileにはローカルファイルのパスまたはリモートファイルのURLを指定します。
     * リモートファイルの読み込みにはphp.iniでallow_url_fopen =Onの設定が必要です。
     * </pre>
     *
     * @param string $file ファイル名
     *
     * @return void
     */
    public function load($file)
    {
        //拡張子はpngチェック
        $this->file = $file;
        $tmpFile = $this->loadRemoteFile($file);
        $this->getImageInfo();
        $this->_log->log('$tmpFile', $tmpFile);
        BEAR_Img::$deleteFiles[] = $tmpFile;
        /** @noinspection PhpUndefinedFunctionInspection */
        $this->surface = cairo_image_surface_create_from_png($tmpFile);
        if (!is_resource($this->surface)) {
            $fileSize = (file_exists($file)) ? filesize($file) : 'none';
            $this->_thisError("load", "cairo_image_surface_create_from_png filse_size=[{$fileSize}] file=[{$file}] ");
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $this->image = cairo_create($this->surface);
        if (!is_resource($this->image)) {
            $this->_thisError('load', 'cairo_create');
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_paint_with_alpha($this->image, 0);
        /** @noinspection PhpUndefinedFunctionInspection */
        $this->_srcWidth = cairo_image_surface_get_width($this->surface);
        /** @noinspection PhpUndefinedFunctionInspection */
        $this->_srcHeight = cairo_image_surface_get_height($this->surface);
    }

    /**
     * 画像を合成
     *
     * 画像ファイル(PNG)を合成します。
     *
     * @param string $file  ファイル名
     * @param int    $x     X座標
     * @param int    $y     Y座標
     * @param float  $alpha アルファブレンディング(0..1)
     *
     * @return void
     */
    public function addImage($file, $x = 0, $y = 0, $alpha = 1.0)
    {
        $file = $this->loadRemoteFile($file);
        /** @noinspection PhpUndefinedFunctionInspection */
        $surfce = cairo_image_surface_create_from_png($file);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_set_source_surface($this->image, $surfce, $x, $y);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_paint_with_alpha($this->image, $alpha);
    }

    /**
     * テキストを合成
     *
     * <pre>
     * 指定位置にテキストを追加します。$alignに右寄せ（_BEAR_ALIGN_RIGHT)を指定
     * すると$xは右からのスペースになります。fontはターミナルでfc-listで得られるフ
     * ォントの名前を使用します。イタリックは$slantにCAIRO_FONT_SLANT_ITALIC,
     *  ボールドは$weightにCAIRO_FONT_WEIGHT_BOLDを指定します。
     * </pre>
     *
     * @param string $text      テキスト
     * @param int    $x         X座標
     * @param int    $y         Y座標
     * @param int    $size      フォントサイズ
     * @param string $align     BEAR_Img::LEFT | BEAR_Img::CENTER | BEAR_Img::RIGHT
     * @param mixed  $colorOne  内側カラー array($r, $g, $b)
     * @param mixed  $colorTwo  アウトラインカラー array($r, $g, $b)
     * @param string $font      フォント
     * @param float  $textAlpha アルファブレンディング(0..1)
     * @param float  $lineWidth ラインの幅
     * @param int    $slant     CAIRO_FONT_SLANT_NORMAL | CAIRO_FONT_SLANT_ITALIC
     * @param int    $weight    CAIRO_FONT_WEIGHT_NORMAL | CAIRO_FONT_WEIGHT_BOLD
     */
    public function addText(
        $text,
        $x = 0,
        $y = 0,
        $size = 120,
        $align = BEAR_Img::LEFT,
        $colorOne = false,
        $colorTwo = false,
        $font = 'Arial',
        $textAlpha = 0.85,
        $lineWidth = 0.75,
        /** @noinspection PhpUndefinedConstantInspection */
        $slant = CAIRO_FONT_SLANT_NORMAL,
        /** @noinspection PhpUndefinedConstantInspection */
        $weight = CAIRO_FONT_WEIGHT_NORMAL
    ) {
        //フォントカラー
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_set_source_rgb($this->image, 0.0, 0.0, 1.0);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_select_font_face($this->image, $font, $slant, $weight);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_set_font_size($this->image, $size);
        /** @noinspection PhpUndefinedFunctionInspection */
        $this->_fontInfo = cairo_text_extents($this->image, $text);
        $this->_log->log('cairo _fontInfo', $this->_fontInfo);
        switch ($align) {
            case BEAR_Img::CENTER :
                $x = $this->_srcWidth / 2 - $this->_fontInfo['x_advance'] / 2 + $x;
                break;
            case BEAR_Img::RIGHT :
                $x = $this->_srcWidth - $this->_fontInfo['x_advance'] - $x;
                break;
            case BEAR_Img::LEFT :
            default :
                break;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_move_to($this->image, $x, $y + $size);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_text_path($this->image, $text);
        //テキスト中身
        if ($colorOne) {
            $colorOneZero = $colorOne[0] / 255;
            $colorOneOne = $colorOne[1] / 255;
            $colorOneTwo = $colorOne[2] / 255;
            /** @noinspection PhpUndefinedFunctionInspection */
            cairo_set_source_rgba($this->image, $colorOneZero, $colorOneOne, $colorOneTwo, $textAlpha);
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            cairo_set_source_rgba($this->image, 1, 1, 1, $textAlpha);
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_fill_preserve($this->image);
        //テキストボーダー
        if ($colorTwo) {
            $colorTwoZero = $colorTwo[0] / 255;
            $colorTwoOne = $colorTwo[1] / 255;
            $colorTwoTwo = $colorTwo[2] / 255;
            /** @noinspection PhpUndefinedFunctionInspection */
            cairo_set_source_rgba($this->image, $colorTwoZero, $colorTwoOne, $colorTwoTwo, $textAlpha);
        } else {
            /** @noinspection PhpUndefinedFunctionInspection */
            cairo_set_source_rgba($this->image, 0, 0, 1, $textAlpha);
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_set_line_width($this->image, $lineWidth);
        //cairo_stroke_preserve($this->image);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_stroke($this->image);
        //cairo_show_page($this->image);
    }

    /**
     * 画像表示
     *
     * <pre>
     * image/pngヘッダーを出力してPNG画像を出力します。
     * cairoはpngしか出力できません。
     * </pre>
     *
     * @return void
     */
    public function show()
    {
        header("Content-type: " . 'image/png');
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_surface_show_png($this->surface);
    }

    /**
     * 画像保存
     *
     * CairoのPNG画像を保存します。
     *
     * @param string $filePath 保存画像のファイルパス
     * @param string $format   画像ファイルのフォーマット
     *
     * @return void
     */
    public function save($filePath, $format = 'png')
    {
        unset($format);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_surface_write_to_png($this->surface, $filePath);
        $isSaved = file_exists($filePath);
        $log = array(
            'isSaved' => $isSaved,
            'surface' => $this->surface,
            'is_res' => is_resource($this->surface),
            'file path' => $filePath,
            'file_exists' => file_exists($filePath),
            'file size' => filesize($filePath)
        );
        $this->_log->log('cairo_surface_write_to_png', $log);
        /** @noinspection PhpUndefinedFunctionInspection */
        cairo_surface_destroy($this->surface);
    }

    /**
     * エラー終了
     *
     * <pre>
     * エラー終了します。
     * 運用でクローラーにキャッシュされないために503ヘッダーを出力しています
     * </pre>
     *
     * @param string $errorFunc コール元のメソッド名
     * @param mixed  $msg       エラーメッセージ
     *
     * @throws BEAR_Exception
     */
    private function _thisError($errorFunc, $msg = false)
    {
        //エラーヘッダー
        header('HTTP/1.0 503 Service Temporarily Unavailable.');
        $isRes = (is_resource($this->image)) ? 'true' : false;
        $info = compact('msg', 'errorFunc', 'isRes');
        throw $this->_exception($msg, array('info' => $info));
    }
}
