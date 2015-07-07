<?php
/**
 * BEAR
 *
 * PHP versions 5
 *
 * @category  BEAR
 * @package   BEAR_Img
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 */

/**
 * イメージ
 *
 * 画像を取り扱うクラスです。画像エンジンにGD2, iMagick(ImageMagick + GraphickMagick),
 * Cairoが選べ切り替えて使う事ができます。
 *
 * @category  BEAR
 * @package   BEAR_Img
 * @author    Akihito Koriyama <akihito.koriyama@gmail.com>
 * @copyright 2008-2015 Akihito Koriyama  All rights reserved.
 * @license   http://opensource.org/licenses/bsd-license.php BSD
 * @version    @package_version@
 * @link      https://github.com/bearsaturday
 *
 * @instance singleton
 *
 * @config mixed adapter イメージアダプター　stringならイメージアダプタークラス
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
    const ADAPTER_GD = '1';

    /**
     * iMagick
     */
    const ADAPTER_MAGICK = '2';

    /**
     * Cairo
     */
    const ADAPTER_CAIRO = '3';

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
    public static $deleteFiles;

    /**
     * Constructor
     *
     * @param array $config
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
     * Constructorでシャットダウン時に実行する関数として登録され実行されます。
     * </pre>
     *
     * @return void
     * @access private
     * @static
     */
    public static function onShutdown()
    {
        if (!is_array(self::$deleteFiles)) {
            return;
        }
        foreach (self::$deleteFiles as $deleteFile) {
            if (is_string($deleteFile)) {
                unlink($deleteFile);
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
     * @return BEAR_Img_Adapter_GD | BEAR_Img_Adapter_Magick | BEAR_Img_Adapter_Cairo
     * @throws BEAR_Img_Exception
     */
    public function factory()
    {
        if (self::$_instance) {
            return self::$_instance;
        }
        PEAR::registerShutdownFunc(array('BEAR_Img', 'onShutdown'));
        $adapter = $this->_config['adapter'];
        switch ($this->_config['adapter']) {
            case self::ADAPTER_GD:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_GD');
                break;
            case self::ADAPTER_MAGICK:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Magick');
                break;
            case self::ADAPTER_CAIRO:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Cairo');
                break;
            default:
                if (is_string($adapter)) {
                    self::$_instance = BEAR::dependency('App_Img_Adapter_' . $this->_config['adapter']);
                    break;
                }
                $options = array('config' => $this->_config);
                throw $this->_exception('Invalid Image Adapter', $options);
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
     * @param string $adapter self::ADAPTER_GD | self::ADAPTER_MAGICK | self::ADAPTER_CAIRO
     *
     * @return BEAR_Img_Adapter_GD | BEAR_Img_Adapter_Magick | BEAR_Img_Adapter_Cairo
     */
    public static function changeAdapter($adapter)
    {
        // 保存
        $tmpFile = self::$_instance->getTmpFileName();
        self::$_instance->save($tmpFile, 'png');
        self::$deleteFiles[] = $tmpFile;
        // イメージインスタンス
        switch ($adapter) {
            case self::ADAPTER_GD:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_GD');
                break;
            case self::ADAPTER_MAGICK:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Magick');
                break;
            case self::ADAPTER_CAIRO:
                self::$_instance = BEAR::dependency('BEAR_Img_Adapter_Cairo');
                break;
            default:
                trigger_error("No engine supported $adapter");
        }
        self::$_instance->load($tmpFile);

        return self::$_instance;
    }

    /**
     * @param $adapter
     *
     * @deprecated
     */
    public static function changeInstance($adapter)
    {
        self::changeAdapter($adapter);
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
