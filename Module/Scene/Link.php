<?php

namespace Leon\BswBundle\Module\Scene;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\GetSetter;

trait Link
{
    use GetSetter\Label;
    use GetSetter\Icon;
    use GetSetter\Route;
    use GetSetter\RootClick;
    use GetSetter\Click;
    use GetSetter\Args;
    use GetSetter\Script;
    use GetSetter\Url;
    use GetSetter\Confirm;
    use GetSetter\ConfirmCheckbox;
    use GetSetter\BeforeOriginal;
    use GetSetter\AfterOriginal;
    use GetSetter\Checked;

    /**
     * @return string
     */
    public function getData(): string
    {
        $data = [
            'route'    => $this->getRoute(),
            'location' => $this->getUrl(),
            'function' => $this->getClick(),
        ];

        $args = $this->getArgs();
        $args['confirmCheckbox'] = $this->getConfirmCheckbox();
        if ($confirm = $this->getConfirm()) {
            $args['confirm'] = $confirm;
        }

        return Helper::jsonStringify(array_merge($data, $args));
    }
}