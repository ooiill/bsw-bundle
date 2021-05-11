<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Form\Entity\Button;

class ButtonScene extends Button
{
    /**
     * @param null|string $title
     *
     * @return ButtonScene
     */
    public function setTitle(?string $title = null)
    {
        return $this->appendArgs(['title' => $title]);
    }

    /**
     * @return ButtonScene
     */
    public function setNoTitle()
    {
        return $this->appendArgs(['title' => false]);
    }

    /**
     * @return ButtonScene
     */
    public function setNoIcon()
    {
        return $this->setIcon(null);
    }

    /**
     * @return ButtonScene
     */
    public function setThemeLink()
    {
        return $this->setType(Abs::THEME_LINK)->appendStyle(['padding' => '0', 'margin' => '3px 0']);
    }

    /**
     * @param bool $want
     *
     * @return ButtonScene
     */
    public function setIWantToKnowHeight(bool $want = true)
    {
        return $this->appendArgs(['debugRealHeight' => $want]);
    }

    /**
     * @return ButtonScene
     */
    public function setAutoTitle()
    {
        return $this->appendArgs(['title' => $this->getLabel() ?: null]);
    }

    /**
     * @param string $shape
     *
     * @return ButtonScene
     */
    public function setShowIframeShape(string $shape = Abs::SHAPE_MODAL)
    {
        return $this->appendArgs(['shape' => $shape]);
    }

    /**
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function setPlacement(string $placement = Abs::POS_LEFT)
    {
        return $this->appendArgs(['placement' => $placement]);
    }

    /**
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function setDrawer(string $placement = Abs::POS_LEFT)
    {
        return $this->setShowIframeShape(Abs::SHAPE_DRAWER)->setPlacement($placement);
    }

    /**
     * @param int|null $width
     *
     * @return ButtonScene
     */
    public function setWidth(?int $width = null)
    {
        return $this->appendArgs(['width' => $width]);
    }

    /**
     * @param int|null $height
     * @param bool     $disableAuthHeight
     *
     * @return ButtonScene
     */
    public function setHeight(?int $height = null, bool $disableAuthHeight = false)
    {
        $button = $this->appendArgs(['height' => $height]);

        return $disableAuthHeight ? $button->setDisableAutoHeight() : $button;
    }

    /**
     * @param int|null $width
     * @param int|null $height
     * @param bool     $disableAuthHeight
     *
     * @return ButtonScene
     */
    public function setWidthHeight(?int $width = null, ?int $height = null, bool $disableAuthHeight = false)
    {
        return $this->setWidth($width)->setHeight($height, $disableAuthHeight);
    }

    /**
     * @param int $min
     *
     * @return ButtonScene
     */
    public function setAutoMinHeight(int $min)
    {
        return $this->appendArgs(['minHeight' => max($min, 0)]);
    }

    /**
     * @param int $max
     *
     * @return ButtonScene
     */
    public function setAutoMaxHeight(int $max)
    {
        return $this->appendArgs(['maxHeight' => $max]);
    }

    /**
     * @param int $offset
     *
     * @return ButtonScene
     */
    public function setAutoHeightOffset(int $offset = 100)
    {
        if (!($height = $this->getArgsItem('height'))) {
            return $this;
        }

        return $this->setAutoMinHeight($height - $offset)->setAutoMaxHeight($height + $offset);
    }

    /**
     * @param bool $allow
     *
     * @return ButtonScene
     */
    public function setAutoHeightOverOffset(bool $allow = false)
    {
        return $this->appendArgs(['overOffset' => $allow ? 'yes' : 'no']);
    }

    /**
     * @return ButtonScene
     */
    public function setDisableAutoHeight()
    {
        return $this->setAutoHeightOverOffset(false)->setAutoHeightOffset(0);
    }

    /**
     * @param int|null $id
     *
     * @return ButtonScene
     */
    public function setId(?int $id = null)
    {
        return $id ? $this->appendArgs(['id' => $id]) : $this;
    }

    /**
     * @param array $fill
     *
     * @return ButtonScene
     */
    public function setFill(array $fill)
    {
        return $this->appendArgs(['fill' => $fill]);
    }

    /**
     * @param bool $closeEasy
     *
     * @return ButtonScene
     */
    public function setShowIframeCloseEasy(bool $closeEasy = true)
    {
        return $this->appendArgs(
            [
                'closable'     => $closeEasy ? false : true,
                'keyboard'     => $closeEasy ? true : false,
                'maskClosable' => $closeEasy ? true : false,
            ]
        );
    }

    /**
     * @return ButtonScene
     */
    public function setShowIframeTitleCenter()
    {
        return $this->appendArgs(['class' => 'bsw-modal-title-center']);
    }

    /**
     * @param string $function
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function setShowIframeWhenDone(string $function, array $params = [])
    {
        return $this->appendArgs(['afterShow' => $function, 'afterShowArgs' => $params]);
    }

    /**
     * @param bool $closeable
     *
     * @return ButtonScene
     */
    public function setCloseable(bool $closeable = true)
    {
        return $this->appendArgs(['closable' => $closeable]);
    }

    /**
     * @param string $text
     *
     * @return ButtonScene
     */
    public function setOkText(string $text)
    {
        return $this->appendArgs(['okText' => $text]);
    }

    /**
     * @param string $text
     *
     * @return ButtonScene
     */
    public function setCancelText(string $text)
    {
        return $this->appendArgs(['cancelText' => $text]);
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModal(string $content)
    {
        return $this->setClick('showModal')->appendArgs(['content' => $content]);
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentMessage(string $content, string $theme = Abs::TAG_CLASSIFY_INFO)
    {
        return $this->setClick('showMessage')->appendArgs(['content' => $content, 'classify' => $theme]);
    }
    
    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawer(string $content)
    {
        return $this->setClick('showDrawer')->appendArgs(['content' => $content]);
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModalNoTitle(string $content)
    {
        return $this->setContentModal($content)->setNoTitle();
    }

    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawerNoTitle(string $content)
    {
        return $this->setContentDrawer($content)->setNoTitle();
    }

    /**
     * @param string $content
     *
     * @return ButtonScene
     */
    public function setContentModalAutoTitle(string $content)
    {
        return $this->setContentModal($content)->setAutoTitle();
    }

    /**
     * @param string $content
     *
     * @return  ButtonScene
     */
    public function setContentDrawerAutoTitle(string $content)
    {
        return $this->setContentDrawer($content)->setAutoTitle();
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModal(string $route)
    {
        return $this->setRoute($route)->setClick('showIFrame');
    }

    /**
     * @param string $route
     * @param string $placement
     *
     * @return  ButtonScene
     */
    public function setRouteDrawer(string $route, string $placement = Abs::POS_LEFT)
    {
        return $this->setRouteModal($route)->setDrawer($placement);
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModalNoTitle(string $route)
    {
        return $this->setRouteModal($route)->setNoTitle();
    }

    /**
     * @param string $route
     *
     * @return  ButtonScene
     */
    public function setRouteDrawerNoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setNoTitle();
    }

    /**
     * @param string $route
     *
     * @return ButtonScene
     */
    public function setRouteModalAutoTitle(string $route)
    {
        return $this->setRouteModal($route)->setAutoTitle();
    }

    /**
     * @param string $route
     *
     * @return  ButtonScene
     */
    public function setRouteDrawerAutoTitle(string $route)
    {
        return $this->setRouteDrawer($route)->setAutoTitle();
    }

    /**
     * @param bool $want
     *
     * @return ButtonScene
     */
    public function setParentWindow(bool $want = true)
    {
        return $this->appendArgs(['iframe' => $want]);
    }

    /**
     * @param bool $close
     *
     * @return ButtonScene
     */
    public function setClosePrevModal(bool $close = false)
    {
        return $this->setParentWindow()->appendArgs(['closePrevModal' => $close]);
    }

    /**
     * @param bool $close
     *
     * @return ButtonScene
     */
    public function setClosePrevDrawer(bool $close = false)
    {
        return $this->setParentWindow()->appendArgs(['closePrevDrawer' => $close]);
    }

    /**
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function setAjaxRequest(string $route, ?int $id = null)
    {
        $button = $this->setRoute($route)->setClick('requestByAjax')->appendArgs(['refresh' => true]);

        return $id ? $button->setId($id) : $button;
    }

    //
    // === 常用场景 ===
    //

    /**
     * 在列表页面的表格中渲染排序触发点 (Modal)
     *
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneCharmSortModal(string $route, int $id)
    {
        return $this
            ->setThemeLink()
            ->setIcon('b:icon-icon-72')
            ->setSize(Abs::SIZE_SMALL)
            ->setWidthHeight(Abs::MEDIA_XS, 222)
            ->setRouteModalNoTitle($route)
            ->setId($id);
    }

    /**
     * 在列表页面的表格中渲染排序触发点 (Drawer)
     *
     * @param string $route
     * @param int    $id
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneCharmSortDrawer(string $route, int $id, string $placement = Abs::POS_LEFT)
    {
        return $this->sceneCharmSortModal($route, $id)->setDrawer($placement);
    }

    /**
     * 渲染跳转新增页面触发点 (Modal)
     *
     * @param string $route
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyModal(string $route)
    {
        return $this
            ->setIcon('a:plus')
            ->setRouteModalAutoTitle($route)
            ->setWidthHeight(Abs::MEDIA_SM, 650);
    }

    /**
     * 渲染跳转新增页面触发点 (Drawer)
     *
     * @param string $route
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyDrawer(string $route, string $placement = Abs::POS_LEFT)
    {
        return $this->sceneOperateNewlyModal($route)->setDrawer($placement);
    }

    /**
     * 渲染跳转编辑页面触发点 (Drawer)
     *
     * @param string $route
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyModal(string $route, int $id)
    {
        return $this->sceneOperateNewlyModal($route)->setIcon('b:icon-bianji1')->setId($id);
    }

    /**
     * 渲染跳转编辑页面触发点 (Drawer)
     *
     * @param string $route
     * @param int    $id
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyDrawer(string $route, int $id, string $placement = Abs::POS_LEFT)
    {
        return $this->sceneOperateModifyModal($route, $id)->setDrawer($placement);
    }

    /**
     * 携带默认填充参数渲染跳转新增页面触发点 (Modal)
     *
     * @param string $route
     * @param array  $fill
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyWithFillModal(string $route, array $fill)
    {
        return $this->sceneOperateNewlyModal($route)->setIcon('b:icon-add')->setFill($fill);
    }

    /**
     * 携带默认填充参数渲染跳转新增页面触发点 (Drawer)
     *
     * @param string $route
     * @param array  $fill
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneOperateNewlyWithFillDrawer(string $route, array $fill, string $placement = Abs::POS_LEFT)
    {
        return $this->sceneOperateNewlyWithFillModal($route, $fill)->setDrawer($placement);
    }

    /**
     * 携带默认填充参数渲染跳转编辑页面触发点 (Modal)
     *
     * @param string $route
     * @param array  $fill
     * @param int    $id
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyWithFillModal(string $route, array $fill, int $id)
    {
        return $this->sceneOperateNewlyWithFillModal($route, $fill)->setId($id);
    }

    /**
     * 携带默认填充参数渲染跳转编辑页面触发点 (Drawer)
     *
     * @param string $route
     * @param array  $fill
     * @param int    $id
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneOperateModifyWithFillDrawer(
        string $route,
        array $fill,
        int $id,
        string $placement = Abs::POS_LEFT
    ) {
        return $this->sceneOperateModifyWithFillModal($route, $fill, $id)->setDrawer($placement);
    }

    /**
     * 在列表页面的表格中渲染内容弹窗触发点 (Modal)
     *
     * @param string $content
     * @param array  $options
     *
     * @return ButtonScene
     */
    public function sceneCharmContentModal(string $content, array $options = [])
    {
        $content = Html::tag(
            'pre',
            $content,
            ['class' => 'bsw-pre bsw-long-text']
        );

        return $this
            ->setContentModal($content)
            ->setSize(Abs::SIZE_SMALL)
            ->setType(Abs::THEME_DEFAULT)
            ->setAutoTitle()
            ->setShowIframeCloseEasy()
            ->appendArgs($options);
    }

    /**
     * 在列表页面的表格中渲染内容弹窗触发点 (Drawer)
     *
     * @param string $content
     * @param array  $options
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneCharmContentDrawer(string $content, array $options = [], string $placement = Abs::POS_LEFT)
    {
        return $this
            ->sceneCharmContentModal($content, $options)
            ->setClick('showDrawer')
            ->setPlacement($placement);
    }

    /**
     * 在列表页面的表格中渲染Json弹窗触发点 (Modal)
     *
     * @param array|string $content
     * @param array        $options
     *
     * @return ButtonScene
     */
    public function sceneCharmCodeModal($content, array $options = [])
    {
        $content = Helper::formatPrintJson($content, 2, ': ');
        $content = Html::cleanHtml($content, true);
        $language = $options['language'] ?? 'json';
        $content = Html::tag('code', $content, ['class' => "language-{$language}"]);

        return $this
            ->sceneCharmContentModal($content, $options)
            ->setShowIframeWhenDone('initHighlightBlock', ['selector' => '.ant-modal-body code'])
            ->setWidth(600)
            ->setNoTitle();
    }

    /**
     * 在列表页面的表格中渲染Json弹窗触发点 (Drawer)
     *
     * @param array|string $content
     * @param array        $options
     * @param string       $placement
     *
     * @return ButtonScene
     */
    public function sceneCharmCodeDrawer($content, array $options = [], string $placement = Abs::POS_LEFT)
    {
        return $this
            ->sceneCharmCodeModal($content, $options)
            ->setShowIframeWhenDone('initHighlightBlock', ['selector' => '.ant-drawer-body code'])
            ->setClick('showDrawer')
            ->setPlacement($placement);
    }

    /**
     * 渲染Ajax删除触发点
     *
     * @param string      $route
     * @param int|null    $id
     * @param string|null $confirm
     *
     * @return ButtonScene
     */
    public function sceneRemoveByAjax(string $route, ?int $id = null, ?string $confirm = null)
    {
        return $this
            ->setType(Abs::THEME_DANGER)
            ->setIcon('b:icon-delete1')
            ->setId($id)
            ->setAjaxRequest($route)
            ->setConfirm($confirm);
    }

    /**
     * 渲染超链接触发点
     *
     * @param string $route
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function sceneLink(string $route, array $params = [])
    {
        return $this->setRoute($route)
            ->appendArgs($params)
            ->setSize(Abs::SIZE_DEFAULT)
            ->setThemeLink();
    }

    /**
     * 渲染图标超链接触发点
     *
     * @param string $route
     * @param string $icon
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function sceneIconLink(string $route, string $icon, array $params = [])
    {
        return $this->sceneLink($route, $params)->setIcon($icon)->setSize(Abs::SIZE_DEFAULT);
    }

    /**
     * 渲染图标超链接触发点 (Modal)
     *
     * @param string $route
     * @param string $icon
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function sceneIconLinkModal(string $route, string $icon, array $params = [])
    {
        return $this->sceneIconLink($route, $icon, $params)->setClick('showIFrame');
    }

    /**
     * 渲染图标超链接触发点 (Drawer)
     *
     * @param string $route
     * @param string $icon
     * @param array  $params
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneIconLinkDrawer(
        string $route,
        string $icon,
        array $params = [],
        string $placement = Abs::POS_LEFT
    ) {
        return $this->sceneIconLinkModal($route, $icon, $params)->setDrawer($placement);
    }

    /**
     * 渲染超链接打开弹窗触发点 (Modal)
     *
     * @param string $route
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function sceneLinkModal(string $route, array $params = [])
    {
        return $this->sceneLink($route, $params)->setClick('showIFrame');
    }

    /**
     * 渲染超链接打开弹窗触发点 (Drawer)
     *
     * @param string $route
     * @param array  $params
     * @param string $placement
     *
     * @return ButtonScene
     */
    public function sceneLinkDrawer(string $route, array $params = [], string $placement = Abs::POS_LEFT)
    {
        return $this->sceneLinkModal($route, $params)->setDrawer($placement);
    }

    /**
     * @param string $route
     * @param array  $params
     *
     * @return ButtonScene
     */
    public function sceneLinkInIframe(string $route, array $params = [])
    {
        return $this->sceneLink($route, $params)->appendArgs(['iframe' => true]);
    }
}