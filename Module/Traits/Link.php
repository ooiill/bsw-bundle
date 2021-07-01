<?php

namespace Leon\BswBundle\Module\Traits;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Form\Entity\Traits as Form;

trait Link
{
    use Form\Label;
    use Form\Icon;
    use Form\Route;
    use Form\RootClick;
    use Form\Click;
    use Form\Args;
    use Form\Script;
    use Form\Url;
    use Form\Confirm;
    use Form\ConfirmCheckbox;

    /**
     * @var string
     */
    public $beforeOriginal;

    /**
     * @var string
     */
    public $afterOriginal;

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

    /**
     * @return string
     */
    public function getBeforeOriginal(): ?string
    {
        return $this->beforeOriginal;
    }

    /**
     * @param string $beforeOriginal
     *
     * @return $this
     */
    public function setBeforeOriginal(string $beforeOriginal)
    {
        $this->beforeOriginal = $beforeOriginal;

        return $this;
    }

    /**
     * @return string
     */
    public function getAfterOriginal(): ?string
    {
        return $this->afterOriginal;
    }

    /**
     * @param string $afterOriginal
     *
     * @return $this
     */
    public function setAfterOriginal(string $afterOriginal)
    {
        $this->afterOriginal = $afterOriginal;

        return $this;
    }
}