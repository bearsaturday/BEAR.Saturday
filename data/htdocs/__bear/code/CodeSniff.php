<?php

/**
 * CS用クラス
 *
 */
class BEAR_Dev_CodeSniff
{

    public static function showList($path, $query = 'do')
    {

        $list = self::getFilesList($path);
        echo '<p>Total: ' . count($list) . ' files</p>';
        foreach ($list as $file) {
            echo "<ul><a href=\"/__bear/code/source.php?$query=$file\" target=\"phpcs\">{$file}</a></ul>";
        }
    }

    /**
     * 実行
     *
     * @param string $path パス
     *
     * @return void
     */
    public static function process($path)
    {
        require_once 'PHP/CodeSniffer.php';
        require_once 'PHP/CodeSniffer/Reports/Full.php';
        $phpcs = new PHP_CodeSniffer();
        try {
            $phpcs->process($path, 'BEAR');
            echo "<pre><code>";
            echo "<div class='info'>BEAR Convention</div>";
            $fileViolations = $phpcs->getFilesErrors();
            $report = new PHP_CodeSniffer_Reporting();
            $report->printReport('Summary', $fileViolations, true, null, 120);
            $report->printReport('Full', $fileViolations, false, null, 120);
            echo "</code></pre>";
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * ファイル名の再帰取得
     *
     * @param string $path ディレクトリパス
     *
     * @return array
     */
    private static function getFilesList($path)
    {
        static $_files = array();

        $files = 0;
        $dir = opendir($path);
        while (($file = readdir($dir)) !== false) {
            if ($file[0] == '.') {
                continue;
            }
            if (strpos($path . DIRECTORY_SEPARATOR . $file, '__bear') !== false
                || strpos($path . DIRECTORY_SEPARATOR . $file, '__panda') !== false
                || strpos($path . DIRECTORY_SEPARATOR . $file, '__edit') !== false) {
                continue;
            }
            #
            $fileParts = explode('.', $path . DIRECTORY_SEPARATOR . $file);
            if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                //dir
                self::getFilesList($path . DIRECTORY_SEPARATOR . $file);
            } else {
                //file
                if (array_pop($fileParts) !== 'php') {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (strpos($fullPath, _BEAR_BEAR_HOME) !== false && substr(str_replace(_BEAR_BEAR_HOME . '/', '', $fullPath), 0, 4) !== 'BEAR') {
                    continue;
                }
                $fullPath = str_replace(_BEAR_BEAR_HOME . '/', '', $fullPath);
                $_files[] = str_replace(_BEAR_APP_HOME . '/', '', $fullPath);
            }
        }
        closedir($dir);
        return $_files;
    }
}
