<?php

namespace Leon\BswBundle\Controller\BswDocument;

use App\Kernel;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Entity\Abs;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * @property Kernel $kernel
 */
trait Document
{
    /**
     * @param Arguments $args
     *
     * @return array
     * @throws
     */
    public function documentDataGenerator(Arguments $args)
    {
        try {
            $file = $this->getFilePath("{$args->name}.md", 'doc');
        } catch (Exception $e) {
            throw new Exception("The document is not found");
        }

        $basic = $this->kernel->getBundle(Abs::BSW_BUNDLE)->getPath();
        [$md, $masterMenu, $slaveMenu, $idMapKey] = $this->parseMdInPath(
            "{$basic}/Resources/doc",
            function ($file, $id, $n, $text) use ($args) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($n == 1 && $index = intval($name)) {
                    $roman = Helper::intToRoman($index);
                    $text = "{$roman}. {$text}";
                }

                $url = $this->url($this->cnf->route_document, compact('name'));
                $url = "{$url}#{$id}";

                return [$url, $text];
            }
        );

        $openMenu = 0;
        $keyMapId = array_flip($idMapKey);
        foreach ($masterMenu as $master) {
            if (strpos($master->getUrl(), $args->name) !== false) {
                $openMenu = $master->getId();
            }
        }

        return [
            'toc'          => implode("\n", array_column($md, 'toc')),
            'masterMenu'   => $masterMenu,
            'slaveMenu'    => $slaveMenu,
            'openMenu'     => $openMenu,
            'selectedMenu' => $keyMapId[Helper::getAnchor()] ?? 0,
            'keyMapIdJson' => Helper::jsonStringify($keyMapId),
            'document'     => $md[$file]['content'],
            'useMenu'      => false,
            'footer'       => $this->cnf->copyright,
        ];
    }

    /**
     * Document bsw
     *
     * @Route("/bsw/document/{name}", name="app_bsw_document", requirements={"name": "[a-zA-Z0-9\-\.]+"}, defaults={"name": "1.overview"})
     *
     * @param string $name
     *
     * @return Response
     */
    public function document(string $name): Response
    {
        if (($args = $this->valid(Abs::V_NOTHING)) instanceof Response) {
            return $args;
        }

        $this->appendSrcCssWithKey('markdown', Abs::CSS_MARKDOWN);
        $this->appendSrcCssWithKey('highlight', Abs::CSS_HIGHLIGHT);
        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);

        $this->seoWithAppName = false;

        // $this->cnf->font_symbol = null; // Not load iconfont.js

        return $this->showEmpty('layout/document.html', ['args' => compact('name')]);
    }
}