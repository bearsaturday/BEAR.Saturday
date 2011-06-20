<?php
/**
 * App
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Db
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */

/**
 * Db
 *
 * @category   BEAR
 * @package    BEAR.app
 * @subpackage Db
 * @author     $Author:$ <username@example.com>
 * @license    @license@ http://@license_url@
 * @version    Release: @package_version@ $Id:$
 * @link       http://@link_url@
 */
class App_Form_Untitled
{
    /**
     * Element template
     *
     * @var string
     */
    private static $_elementTemplate = "\n\t\t<li><label class=\"element\"><!-- BEGIN required --><span class=\"required\">*</span><!-- END required -->{label}</label><div class=\"element<!-- BEGIN error -->_error<!-- END error -->\">{element}<!-- BEGIN error --><span class=\"error\">!</span><!-- END error --><!-- BEGIN label_3 -->{label_3}<!-- END label_3 --><!-- BEGIN label_2 --><br /><span style=\"font-size: 80%;\">{label_2}</span><!-- END label_2 --></div></li>";

    /**
     * Inject
     *
     * @return void
     */
    public function onInject()
    {
        $callback = array(__CLASS__, 'onRender');
        $this->_form = array('formName' => 'form', 'callback' => $callback);
    }

    /**
     * Mobile Inject
     *
     * @return void
     */
    public function onInjectMobile()
    {
        self::$_elementTemplate = "\n\t\t<li><label class=\"element\"><!-- BEGIN required --><span class=\"required\">*</span><!-- END required -->{label}</label><div class=\"element<!-- BEGIN error -->_error<!-- END error -->\">{element}<!-- BEGIN error --><span class=\"error\">!</span><!-- END error --><!-- BEGIN label_3 -->{label_3}<!-- END label_3 --><!-- BEGIN label_2 --><br /><span style=\"font-size: 80%;\">{label_2}</span><!-- END label_2 --></div></li>";
        $this->onInject();
    }

    /**
     * Form
     *
     * @return void
     */
    public function build()
    {
        $form = BEAR::dependency('BEAR_Form', $this->_form);
        $form->setDefaults(array('name' => 'Kuma', 'email' => 'kuma@example.com'));
        //  フォームヘッダー
        $form->addElement('header', 'main', '入力(確認）してください');
        //  フォームインプットフィールド
        $form->addElement('text', 'name', '名前', 'size=30 maxlength=30');
        $form->addElement('text', 'email', 'メールアドレス', 'size=30 maxlength=30');
        $form->addElement('textarea', 'body', '感想');
        $form->addElement('submit', '_submit', '送信', '');
        // フィルタと検証ルール
        $form->applyFilter('__ALL__', 'trim');
        $form->applyFilter('__ALL__', 'strip_tags');
        $form->addRule('name', '名前を入力してください', 'required', null, 'client');
        $form->addRule('email', 'emailを入力してください', 'required', null, 'client');
        $form->addRule('email', 'emailの形式で入力してください', 'email', null, 'client');
    }

    /**
     * Custom render
     *
     * @param HTML_QuickForm_Renderer_Tableless $render
     *
     * @return void
     * @see http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless/docs
     */
    public static function onRender(HTML_QuickForm_Renderer_Tableless $render)
    {
        $render->setElementTemplate(self::$_elementTemplate);
    }
}