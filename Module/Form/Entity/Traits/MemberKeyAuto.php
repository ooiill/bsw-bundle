<?php

namespace Leon\BswBundle\Module\Form\Entity\Traits;

trait MemberKeyAuto
{
    /**
     * @var bool
     */
    private $memberKeyAuto = true;

    /**
     * @param bool $memberKeyAuto
     *
     * @return $this
     */
    public function setMemberKeyAuto(bool $memberKeyAuto = true)
    {
        $this->memberKeyAuto = $memberKeyAuto;

        return $this;
    }

    /**
     * @return bool
     */
    public function getMemberKeyAuto(): bool
    {
        return $this->memberKeyAuto;
    }
}