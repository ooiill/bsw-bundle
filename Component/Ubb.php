<?php

namespace Leon\BswBundle\Component;

class Ubb
{
    /**
     * @var array|string
     */
    public $bbsFilterElements = ['id', 'on.*'];

    /**
     * @var array|string
     */
    public $bbsAllowTags = [
        'img',
        'font',
        'div',
        'span',
        'center',
        'strong',
        'blockquote',
        'figure',
        'figcaption',
        'hr|br',
        'em|code|sub|sup',
        'ul|ol|li',
        'table|thead|tbody|tr|td|th',
        'h[1-6]',
        'pre',
        'b|u|s|i|p|a',
        'mark',
    ];

    /**
     * Ubb constructor.
     *
     * @param array $filterElements
     * @param array $allowTags
     */
    public function __construct(array $filterElements = null, array $allowTags = null)
    {
        if ($filterElements) {
            $this->bbsFilterElements = $filterElements;
        }

        if ($allowTags) {
            $allowTags && $this->bbsAllowTags = $allowTags;
        }

        $this->bbsFilterElements = implode('|', $this->bbsFilterElements);
        $this->bbsAllowTags = implode('|', $this->bbsAllowTags);
    }

    /**
     * HTML to UBB
     *
     * @param string $html
     * @param bool   $retainBr
     *
     * @return string
     */
    public function htmlToUbb(string $html, bool $retainBr = false): string
    {
        $html = Html::perfectHtml($html);

        // pretreatment
        if (true === $retainBr) {
            $html = str_replace(
                ["\r", "\n", "\r\n", "\t", PHP_EOL],
                null,
                $html
            );
        }

        $html = preg_replace('/ (' . $this->bbsFilterElements . ')=".*"/iU', null, $html);
        $html = str_replace('&nbsp;', '[ ]', $html);

        // handle tags
        $html = preg_replace('/\<\/(' . $this->bbsAllowTags . ')\>/iU', '[/$1]', $html);
        $html = preg_replace('/\<(' . $this->bbsAllowTags . ')(?:\/)?\>/iU', '[$1]', $html);
        $html = preg_replace('/\<(' . $this->bbsAllowTags . ') (.*)(?:\/)?\>/iU', '[$1 $2]', $html);

        // filter others html
        return Html::cleanHtml($html);
    }

    /**
     * UBB to HTML
     *
     * @param string $ubb
     *
     * @return string
     */
    public function ubbToHtml(string $ubb): string
    {
        // pretreatment
        $ubb = str_replace('[ ]', '&nbsp;', $ubb);

        // handle tags
        $ubb = preg_replace('/\[\/(' . $this->bbsAllowTags . ')\]/iU', '</$1>', $ubb);
        $ubb = preg_replace('/\[(' . $this->bbsAllowTags . ')\]/iU', '<$1>', $ubb);
        $ubb = preg_replace('/\[(' . $this->bbsAllowTags . ') (.*)\]/iU', '<$1 $2>', $ubb);

        return Html::perfectHtml($ubb);
    }
}