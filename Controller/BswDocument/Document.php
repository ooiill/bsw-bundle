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
        return $this->markdownDirectoryParse($args->name, $this->getPath('doc'));
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
        $this->appendSrcCssWithKey('fancy-box', Abs::CSS_FANCY_BOX);

        $this->appendSrcJsWithKey('highlight', Abs::JS_HIGHLIGHT);
        $this->appendSrcJsWithKey('fancy-box', Abs::JS_FANCY_BOX);

        $this->seoWithAppName = false;

        // $this->cnf->font_symbol = null; // Not load iconfont.js

        return $this->showEmpty('layout/document.html', ['args' => compact('name')]);
    }
}