<?php

namespace Leon\BswBundle\Module\Bsw\Header;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Html;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Bsw\Header\Entity\Links;
use Leon\BswBundle\Module\Bsw\Header\Entity\Setting;
use Leon\BswBundle\Module\Bsw\Menu\Entity\Menu;
use Leon\BswBundle\Module\Entity\Abs;

/**
 * @property Input $input
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const MENU     = 'Menu';
    const SETTING  = 'Setting';
    const LINKS    = 'Links';
    const LANGUAGE = 'Language';

    /**
     * @return bool
     */
    public function allowAjax(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function allowIframe(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'header';
    }

    /**
     * @return array
     */
    public function javascript(): ?array
    {
        return ['module-scaffold' => 'diy;module/scaffold.js'];
    }

    /**
     * @return ArgsInput
     */
    public function input(): ArgsInput
    {
        return new Input();
    }

    /**
     * @return ArgsOutput
     */
    public function logic(): ArgsOutput
    {
        $output = new Output($this->input);

        // Menu
        $menu = $this->caller($this->method(), self::MENU, Abs::T_ARRAY, [], $this->arguments($this->input->args));
        $method = $this->method() . self::MENU;

        foreach ($menu as $item) {
            /**
             * @var Menu $item
             */
            Helper::objectInstanceOf($item, Menu::class, "Method {$method}():array items");

            // access control
            $route = trim($item->getRoute());
            if ($route && empty($this->input->access[$route])) {
                continue;
            }

            $args = $item->getArgs();

            // route path
            if ($route) {
                $item->setUrl($this->web->urlSafe($route, $args, 'Build header menu'));
            }

            // javascript
            if ($click = $item->getClick()) {
                foreach ($args as &$value) {
                    $value = str_replace('{ROUTE}', $item->getUrl(), $value);
                }
                $args = Helper::numericValues($args);
                $item->setArgs(array_merge(['function' => $click], $args));
            }

            array_push($output->menu, $item);
        }

        // Setting
        $setting = $this->caller(
            $this->method(),
            self::SETTING,
            Abs::T_ARRAY,
            [],
            $this->arguments($this->input->args)
        );
        $method = $this->method() . self::SETTING;

        foreach ($setting as $item) {
            /**
             * @var Setting $item
             */
            Helper::objectInstanceOf($item, Setting::class, "Method {$method}():array items");

            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Build header setting'));
            array_push($output->setting, $item);
        }

        // Links
        $links = $this->caller($this->method(), self::LINKS, Abs::T_ARRAY, [], $this->arguments($this->input->args));
        $method = $this->method() . self::SETTING;

        foreach ($links as $item) {
            /**
             * @var Links $item
             */
            Helper::objectInstanceOf($item, Links::class, "Method {$method}():array items");

            $item->setScript(Html::scriptBuilder($item->getClick(), $item->getArgs()));
            $item->setUrl($this->web->urlSafe($item->getRoute(), $item->getArgs(), 'Build header links'));
            array_push($output->links, $item);
        }

        // Links
        $output->language = $this->caller(
            $this->method(),
            self::LANGUAGE,
            Abs::T_ARRAY,
            [],
            $this->arguments($this->input->args)
        );

        $output = $this->caller(
            $this->method(),
            self::OUTPUT_ARGS_HANDLER,
            Output::class,
            $output,
            $this->arguments(compact('output'), $this->input->args)
        );

        return $output;
    }
}