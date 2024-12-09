<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;

require_once "shortcode/shortcode.php";
class ShortcodeComponent extends Component
{
    private static $shortcodes = [];

    /**
     * Đăng ký shortcode
     * @param string $name Tên shortcode
     * @param callable $callback Hàm xử lý cho shortcode
     */
    public function addShortcode($name, $callback)
    {
        self::$shortcodes[$name] = $callback;
    }

    /**
     * Render nội dung có chứa shortcode
     * @param string $content Nội dung HTML
     * @return string Nội dung đã xử lý shortcode
     */
    public function render($content)
    {
        foreach (self::$shortcodes as $name => $callback) {
            $pattern = '/\[' . preg_quote($name, '/') . '(\s[^\]]*)?\]/';
            $content = preg_replace_callback($pattern, function ($matches) use ($callback) {
                $attributes = [];
                if (!empty($matches[1])) {
                    preg_match_all('/(\w+)="([^"]*)"/', $matches[1], $attrMatches, PREG_SET_ORDER);
                    foreach ($attrMatches as $attr) {
                        $attributes[$attr[1]] = $attr[2];
                    }
                }
                return call_user_func($callback, $attributes);
            }, $content);
        }
        return $content;
    }

    public function renderTemplatePart($template, $params=null)
    {
        // Giải nén mảng `$params` thành biến
        extract($params);

        // Bắt đầu output buffering
        ob_start();
        ob_implicit_flush(false);

        try {
            $filePath = $this->getViewPath($template);
            require $filePath; // Load file PHP
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }

        // Lấy nội dung buffer và dừng buffering
        return ob_get_clean();
    }

    /**
     * Get view path of view of current theme
     *
     * @param $viewName
     * @return string view file path
     */
    protected function getViewPath($viewName)
    {
        return \Yii::getAlias("@app/components/shortcode/views/{$viewName}.php");
    }
}