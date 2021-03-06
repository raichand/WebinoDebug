<?php
/**
 * Webino (http://webino.sk/)
 *
 * @link        https://github.com/webino/WebinoDebug/ for the canonical source repository
 * @copyright   Copyright (c) 2014-2017 Webino, s. r. o. (http://webino.sk/)
 * @license     BSD-3-Clause
 */

namespace WebinoDebug\Debugger\Bar;

use WebinoDebug\Exception;

/**
 * Class AbstractPanel
 */
abstract class AbstractPanel
{
    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $dir = __DIR__;

    /**
     * {@inheritdoc}
     */
    public function getTab()
    {
        return sprintf('<span title="%s" class="tracy-label">%s</span>', $this->title, $this->label);
    }

    /**
     * {@inheritdoc}
     */
    public function createIcon($name, $style = '')
    {
        $data = file_get_contents($this->dir . '/../../../../data/assets/Bar/' . $name . '.png');
        $base64 = 'data:image/png;base64,' . base64_encode($data);
        return '<img src="' . $base64 . '" style="' . $style . '"/>';
    }

    /**
     * @param $name
     * @return string
     */
    public function renderTemplate($name)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        require $this->dir . '/../../../../data/assets/Bar/' . $name . '.phtml';
        return ob_get_clean();
    }
}
