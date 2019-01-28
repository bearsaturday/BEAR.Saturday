<?php
/**
 * This file is part of the BEAR.Saturday package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * イメージ
 *
 * 画像を取り扱うクラスです。画像エンジンにGD2, iMagick(ImageMagick + GraphickMagick),
 * Cairoが選べ切り替えて使う事ができます。
 *
 * @Singleton
 *
 * @config mixed adapter イメージアダプター　stringならイメージアダプタークラス
 */
class BEAR_Img extends BEAR_Factory
{
    /**
     * 外部画像ファイルをフェッチするときのUA
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
     * 消去用テンポラリーファイルリスト配列
     *
     * @var array
     */
    public static $deleteFiles;

    /**
     * シングルトンオブジェクト
     *
     * @var object
     */
    private static $_instance;

    /**
     * Constructor
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
     * @static
     */
    public static function onShutdown()
    {
        if (! is_array(self::$deleteFiles)) {
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
     * @throws BEAR_Img_Exception
     *
     * @return BEAR_Img_Adapter_GD | BEAR_Img_Adapter_Magick | BEAR_Img_Adapter_Cairo
     */
    public function factory()
    {
        if (self::$_instance) {
            return self::$_instance;
        }
        PEAR::registerShutdownFunc(['BEAR_Img', 'onShutdown']);
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
                $options = ['config' => $this->_config];

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
                trigger_error("No engine supported ${adapter}");
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
     */
    public function destoryInstance()
    {
        self::$_instance = null;
    }
}
