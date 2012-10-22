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
 * @version   SVN: Release: $Id: Img.php 1254 2009-12-07 08:02:44Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Img/BEAR_Img.html
 */
/**
 * BEAR_Imgクラス
 *
 * <pre>
 * 画像を取り扱うクラスです。画像エンジンにGD2, iMagick(ImageMagick + GraphickMagick),
 * Cairoが選べ切り替えて使う事ができます。
 * </pre>
 *
 * @category  BEAR
 * @package   BEAR_Img
 * @author    Akihito Koriyama <koriyama@users.sourceforge.jp>
 * @copyright 2008 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: Release: $Id: Img.php 1254 2009-12-07 08:02:44Z koriyama@users.sourceforge.jp $
 * @link      http://api.bear-project.net/BEAR_Img/BEAR_Img.html
 *
 */
class BEAR_Img extends BEAR_Factory
{

    /**
     * 外部画像ファイルをフェッチするときのUA
     *
     */
    const UA = 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';

    /**
     * GD
     */
    const ADAPTOR_GD = '1';

    /**
     * iMagick
     */
    const ADAPTOR_MAGICK = '2';

    /**
     * Cairo
     */
    const ADAPTOR_CAIRO = '3';

    /**
     * テンポラリーファイル作成場所
     *
     */
    const TMP_DIR = '/tmp/misc';

    /**
     * アライン Center
     */
    const CENTER = 'c';

    /**
     *　アライン　Left
     */
    const LEFT = 'l';

    /**
     * アライン Right
     */
    const RIGHT = 'r';

    /**
     * シングルトンオブジェクト
     *
     * @var object
     * @access public
     */
    private static $_instance;

    /**
     * 消去用テンポラリーファイルリスト配列
     *
     * @var array
     */
    static $deleteFiles;

    /**
     * コンストラクタ
     *
     * @param array $config 設定
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * テンポラリーファイルの消去
     *
     * <pre>
     * 画像変換などに使用するテンポラリーファイルを消去します。
     * コンストラクタでシャットダウン時に実行する関数として登録され実行されます。
     * </pre>
     *
     * @return void
     * @access private
     * @static
     */
    public static function onShutdown()
    {
        if (!is_array(BEAR_Img::$deleteFiles)) {
            return;
        }
        foreach (BEAR_Img::$deleteFiles as $deleteFile) {
            if (is_string($deleteFile)) {
                $result = unlink($deleteFile);
                if (!$result && $this->_config['debug']) {
                    error_log('Image temp file can\'t deleted');
                }
            }
        }
    }

    /**
     * インスタンス取得
     *
     * <pre>
     * 指定の画像エンジンで画像処理オブジェクトを返します
     * </pre>
     *
     * @param string $adapter BEAR_Img::ADAPTOR_GD | BEAR_Img::ADAPTOR_MAGICK | BEAR_Img::ADAPTOR_CAIRO
     *
     * @return object
     * @return BEAR_Img_Adapter_GD | BEAR_Img_Adapter_Magick | BEAR_Img_Adapter_Cairo
     */
    public function factory()
    {
        if (self::$_instance) {
            return self::$_instance;
        }
        PEAR::registerShutdownFunc(array('BEAR_Img', 'onShutdown'));
        $adapter = $this->_config['adaptor'];
        switch ($this->_config['adaptor']) {
        case self::ADAPTOR_GD :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_GD');
            break;
        case self::ADAPTOR_MAGICK :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Magick');
            break;
        case self::ADAPTOR_CAIRO :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Cairo');
            break;
        default :
            if (is_string($adapter)) {
                self::$_instance = BEAR::dependency('App_Img_Adapter_' . $this->_config['adaptor']);
                break;
            }
            $options = array('config' => $this->_config);
            throw $this->_exception('Invalid Image Adaptor', $options);
        }
        return self::$_instance;
    }

    /**
     * インスタンス変更
     *
     * <pre>
     * 画像エンジンを変更します。イメージオブジェクトは引き継がれます。
     * GDでjpegを読み込み、Cairoで文字を合成、GDでGIF出力などのように使えます。
     * </pre>
     *
     * @param string $adapter BEAR_Img::ADAPTOR_GD | BEAR_Img::ADAPTOR_MAGICK | BEAR_Img::ADAPTOR_CAIRO
     *
     * @return BEAR_Img_Adapter_GD | BEAR_Img_Adapter_Magick | BEAR_Img_Adapter_Cairo
     */
    public static function changeAdaptor($adapter)
    {
        //保存
        $tmpFile = self::$_instance->getTmpFileName();
        self::$_instance->save($tmpFile, 'png');
        self::$deleteFiles[] = $tmpFile;
        //イメージインスタンス
        switch ($adapter) {
        case self::ADAPTOR_GD :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_GD');
            break;
        case self::ADAPTOR_MAGICK :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Magick');
            break;
        case self::ADAPTOR_CAIRO :
            self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Cairo');
            break;
        default :
            trigger_error("No engine supported $adapter");
        }
        //読み込み
        self::$_instance->load($tmpFile);
        return self::$_instance;
    }

    /**
     * changeAdaptorのエイリアス
     *
     * @deprecated
     */
    public static function changeInstance($adapter) {
        self::changeAdaptor($adapter);
    }
    /**
     * インスタンス消去
     *
     * @return void
     */
    public function destoryInstance()
    {
        self::$_instance = null;
    }
}
