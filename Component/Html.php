<?php

namespace Leon\BswBundle\Component;

use Leon\BswBundle\Module\Entity\Abs;
use DOMDocument;
use DOMNode;

class Html
{
    /**
     * @var array list of void elements (element name => 1)
     */
    public static $voidElements = [
        'area'    => 1,
        'base'    => 1,
        'br'      => 1,
        'col'     => 1,
        'command' => 1,
        'embed'   => 1,
        'hr'      => 1,
        'img'     => 1,
        'input'   => 1,
        'keygen'  => 1,
        'link'    => 1,
        'meta'    => 1,
        'param'   => 1,
        'source'  => 1,
        'track'   => 1,
        'wbr'     => 1,
    ];

    /**
     * @var array the preferred order of attributes in a tag.
     */
    public static $attributeOrder = [
        'type',
        'id',
        'class',
        'name',
        'value',

        'href',
        'src',
        'srcset',
        'form',
        'action',
        'method',

        'selected',
        'checked',
        'readonly',
        'disabled',
        'multiple',

        'size',
        'maxlength',
        'width',
        'height',
        'rows',
        'cols',

        'alt',
        'title',
        'rel',
        'media',
    ];

    /**
     * @var array list of tag attributes that should be specially handled when their values are of array type.
     */
    public static $dataAttributes = ['data', 'data-ng', 'ng'];

    /**
     * Encodes special characters into HTML entities.
     *
     * @param string $content      the content to be encoded
     * @param bool   $doubleEncode whether to encode HTML entities in `$content`. If false,
     *
     * @return string the encoded content
     */
    public static function encode(string $content, bool $doubleEncode = true): string
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * Decodes special HTML entities back to the corresponding characters.
     *
     * @param string $content the content to be decoded
     *
     * @return string the decoded content
     */
    public static function decode(string $content): string
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }

    /**
     * Generates a div
     *
     * @param string $content
     * @param array  $class
     * @param array  $options
     *
     * @return string
     */
    public static function div(string $content, array $class = [], array $options = []): string
    {
        return self::tag('div', $content, array_merge($options, compact('class')));
    }

    /**
     * Generates a p
     *
     * @param string $content
     * @param array  $class
     * @param array  $options
     *
     * @return string
     */
    public static function p(string $content, array $class = [], array $options = []): string
    {
        return self::tag('p', $content, array_merge($options, compact('class')));
    }

    /**
     * Generates a pre
     *
     * @param string $content
     * @param array  $class
     * @param array  $options
     *
     * @return string
     */
    public static function pre(string $content, array $class = [], array $options = []): string
    {
        return self::tag('pre', $content, array_merge($options, compact('class')));
    }

    /**
     * Generates a font
     *
     * @param string      $content
     * @param string|null $color
     * @param string|null $fontSize
     * @param array       $options
     *
     * @return string
     */
    public static function font(
        string $content,
        string $color = null,
        string $fontSize = null,
        array $options = []
    ): string {
        return self::tag(
            'font',
            $content,
            array_merge($options, ['style' => compact('color', 'fontSize')])
        );
    }

    /**
     * Font green
     *
     * @param string      $content
     * @param string|null $fontSize
     * @param array       $options
     *
     * @return string
     */
    public static function fontGreen(string $content, string $fontSize = null, array $options = []): string
    {
        return self::font($content, 'green', $fontSize, $options);
    }

    /**
     * Font green
     *
     * @param string      $content
     * @param string|null $fontSize
     * @param array       $options
     *
     * @return string
     */
    public static function fontRed(string $content, string $fontSize = null, array $options = []): string
    {
        return self::font($content, 'red', $fontSize, $options);
    }

    /**
     * Font green
     *
     * @param string      $content
     * @param string|null $fontSize
     * @param array       $options
     *
     * @return string
     */
    public static function fontOrange(string $content, string $fontSize = null, array $options = []): string
    {
        return self::font($content, 'orange', $fontSize, $options);
    }

    /**
     * Font green
     *
     * @param string      $content
     * @param string|null $fontSize
     * @param array       $options
     *
     * @return string
     */
    public static function fontBlue(string $content, string $fontSize = null, array $options = []): string
    {
        return self::font($content, 'blue', $fontSize, $options);
    }

    /**
     * Generates a i
     *
     * @param string $content
     * @param array  $class
     * @param array  $options
     *
     * @return string
     */
    public static function i(string $content, array $class = [], array $options = []): string
    {
        return self::tag('i', $content, array_merge($options, compact('class')));
    }

    /**
     * Generates a link
     *
     * @param string $content
     * @param string $href
     * @param array  $options
     *
     * @return string
     */
    public static function link(string $content, string $href, array $options = []): string
    {
        return self::tag('a', $content, array_merge($options, compact('href')));
    }

    /**
     * Generates a complete HTML tag.
     *
     * @param string|bool|null $name    the tag name.
     * @param string|null      $content the content to be enclosed between the start and end tags.
     * @param array            $options the HTML tag attributes (HTML options) in terms of name-value pairs.
     *
     * @return string the generated HTML tag
     */
    public static function tag($name, $content = '', array $options = [])
    {
        if ($name === null || $name === false) {
            return $content;
        }

        $options = Helper::keyCamelToUnder($options, '-');
        $html = "<$name" . static::renderTagAttributes($options) . '>';

        return isset(static::$voidElements[strtolower($name)]) ? $html : "{$html}{$content}</$name>";
    }

    /**
     * Renders the HTML tag attributes.
     *
     * @param array $attributes attributes to be rendered.
     *
     * @return string the rendering result.
     */
    public static function renderTagAttributes(array $attributes): string
    {
        $attributes = self::cleanArrayHtml($attributes);
        if (count($attributes) > 1) {
            $sorted = [];
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " {$name}";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " {$name}-{$n}='" . Helper::jsonStringify($v) . "'";
                        } else {
                            $html .= " {$name}-{$n}=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " {$name}=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " {$name}=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " {$name}='" . Helper::jsonStringify($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " {$name}=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }

    /**
     * Converts a CSS style array into a string representation.
     *
     * @param array $style the CSS style array.
     *
     * @return string the CSS style string.
     */
    public static function cssStyleFromArray(array $style): ?string
    {
        $result = '';
        $style = self::cleanArrayHtml($style);

        foreach ($style as $name => $value) {
            $name = Helper::camelToUnder($name, '-');
            $result .= "{$name}: {$value}; ";
        }

        // return null if empty to avoid rendering the "style" attribute
        return $result === '' ? null : rtrim($result);
    }

    /**
     * Create table
     *
     * @param array $body
     * @param array $head
     * @param array $options
     *
     * @return string
     */
    public static function table(array $body = [], array $head = [], array $options = []): string
    {
        $th = null;
        foreach ($head as $item) {
            $th .= self::tag('th', $item['value'], $item['options'] ?? []);
        }

        $tb = null;
        foreach ($body as $item) {
            $td = null;
            foreach ($item as $v) {
                if (is_callable($v['handler'] ?? null)) {
                    $v['value'] = $v['handler']($v['value']);
                }
                if (is_string($v['tpl'] ?? false)) {
                    $v['value'] = sprintf($v['tpl'], $v['value']);
                }
                $td .= self::tag('td', $v['value'], $v['options'] ?? []);
            }
            $tb .= self::tag('tr', $td);
        }

        $thead = self::tag('thead', self::tag('tr', $th), $options['thead'] ?? []);
        $tbody = self::tag('tbody', $tb, $options['tbody'] ?? []);

        return self::tag('table', "{$thead}{$tbody}", $options['table'] ?? []);
    }

    /**
     * Auto perfect the html
     *
     * @param string $html
     *
     * @return string
     */
    public static function perfectHtml(string $html): string
    {
        // strip fraction of open or close tag from end
        // (e.g. if we take first x characters, we might cut off a tag at the end!)
        $html = preg_replace('/<[^>]*$/', null, $html);

        // put open tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openTags = $result[1];

        // put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closeTags = $result[1];
        $lenOpened = count($openTags);

        // if all tags are closed, we can return
        if (count($closeTags) == $lenOpened) {
            return $html;
        }

        // close tags in reverse order that they were opened
        $openTags = array_reverse($openTags);

        // self closing tags
        $sc = [
            'br',
            'input',
            'img',
            'hr',
            'meta',
            'link',
        ];

        // ,'frame','i-frame','param','area','base','base-font','col'
        // should not skip tags that can have content inside!
        for ($i = 0; $i < $lenOpened; $i++) {
            $ot = strtolower($openTags[$i]);
            if (!in_array($openTags[$i], $closeTags) && !in_array($ot, $sc)) {
                $html .= '</' . $openTags[$i] . '>';
            } else {
                unset($closeTags[array_search($openTags[$i], $closeTags)]);
            }
        }

        return $html;
    }

    /**
     * Remove the html tag
     *
     * @param string|null $str
     * @param bool        $entity
     *
     * @return string|null
     */
    public static function cleanHtml(?string $str, bool $entity = false): ?string
    {
        if (empty($str)) {
            return $str;
        }

        if ($entity) {
            return htmlentities($str);
        }

        return strip_tags(preg_replace('/<\/?([a-z]+)[^>]*>/i', null, $str));
    }

    /**
     * Remove the html tag for array
     *
     * @param array $target
     * @param bool  $entity
     *
     * @return array
     */
    public static function cleanArrayHtml(array $target, bool $entity = false): array
    {
        foreach ($target as &$item) {
            if (is_array($item)) {
                $item = self::cleanArrayHtml($item, $entity);
            } elseif (is_string($item)) {
                $item = self::cleanHtml($item, $entity);
            }
        }

        return $target;
    }

    /**
     * Params builder
     *
     * @param string|array $params
     *
     * @return string
     */
    public static function paramsBuilder($params = null): ?string
    {
        if (!is_scalar($params) && !is_array($params)) {
            return null;
        }

        if (is_string($params)) {
            return addslashes($params);
        }

        if (!is_array($params)) {
            return $params;
        }

        if (Helper::typeofArray($params, Abs::T_ARRAY_ASSOC)) {
            return Helper::jsonStringify($params);
        }

        $items = [];
        foreach ($params as $item) {
            if ($item === 'this') {
                array_push($items, 'this');
            } elseif (is_string($item)) {
                $item = addslashes($item);
                array_push($items, "'{$item}'");
            } elseif (!is_array($item)) {
                array_push($items, $item);
            } else {
                array_push($items, Helper::jsonStringify($item));
            }
        }

        return implode(', ', $items);
    }

    /**
     * Javascript builder
     *
     * @param string       $name
     * @param string|array $params
     *
     * @return string
     */
    public static function scriptBuilder(string $name, $params = null): string
    {
        $params = self::paramsBuilder($params);

        if (empty($name)) {
            return $params;
        }

        return "{$name}({$params})";
    }

    /**
     * Read xml to array
     *
     * @param string $xmlFile
     *
     * @return array
     */
    public static function xmlReader(string $xmlFile): array
    {
        function getArray(DOMNode $node)
        {
            $array = [];

            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attr) {
                    $array[$attr->nodeName] = $attr->nodeValue;
                }
            }

            if ($node->hasChildNodes()) {
                if ($node->childNodes->length == 1) {
                    $array[$node->firstChild->nodeName] = getArray($node->firstChild);
                } else {
                    foreach ($node->childNodes as $childNode) {
                        if ($childNode->nodeType != XML_TEXT_NODE) {
                            $array[$childNode->nodeName][] = getArray($childNode);
                        }
                    }
                }
            } else {
                return $node->nodeValue;
            }

            return $array;
        }

        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));

        return getArray($dom->documentElement);
    }

    /**
     * Compress html
     *
     * @param string $content
     *
     * @return string
     * */
    public static function compressHtml(string $content): string
    {
        $content = str_replace("\r\n", null, $content);
        $content = str_replace("\n", null, $content);
        $content = str_replace("\t", null, $content);

        $pattern = [
            "/> *([^ ]*) *</",
            "/[\s]+/",
            "/<!--[^!]*-->/",
            "/\" /",
            "/ \"/",
            "/\*[^*]*\*/",
        ];
        $replace = [
            '>\\1<',
            ' ',
            null,
            '"',
            '"',
            null,
            null,
        ];

        return preg_replace($pattern, $replace, $content);
    }

    /**
     * Array to xml
     *
     * @access public
     *
     * @param array $params
     * @param bool  $weChatModel
     *
     * @return string
     */
    public static function arrayToXml(array $params, bool $weChatModel = false): string
    {
        $params = (array)$params;
        $xml = '<xml>';
        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
            } else {
                $begin = $weChatModel ? '<![CDATA[' : null;
                $end = $weChatModel ? ']]>' : null;
                $xml .= '<' . $key . '>' . $begin . $val . $end . '</' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * Xml to array
     *
     * @access public
     *
     * @param string $xml
     *
     * @return array
     */
    public static function xmlToArray(string $xml): array
    {
        if (empty($xml)) {
            return [];
        }

        libxml_disable_entity_loader(true);
        $object = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($object), true);
    }

    /**
     * Send post by form
     *
     * @access public
     *
     * @param string $url
     * @param array  $params
     *
     * @return string
     */
    public static function postForm(string $url, array $params): string
    {
        $html = "<form id='form' name='form' action='{$url}' method='POST'>";
        foreach ($params as $key => $value) {
            $value = str_replace("'", "&apos;", $value);
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
        }

        // submit must with not attribute `name`
        $html .= "<input type='submit' value='ok' style='display:none;'></form>";
        $html .= "<script>document.forms['form'].submit();</script>";

        return $html;
    }
}