<?php

namespace Leon\BswBundle\Module\Bsw\Persistence\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Scene\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Password;

class NewPassword extends Tailor
{
    /**
     * @var string
     */
    protected $newField;

    /**
     * @return void
     */
    public function initial()
    {
        parent::initial();

        $this->newField = "{$this->fieldCamel}NewPassword";
    }

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(Arguments $args): array
    {
        $args->target[$this->newField] = Helper::merge(
            [
                'label'  => $this->label,
                'sort'   => $args->persistAnnotation[$this->fieldCamel]['sort'] + 0.01,
                'column' => 8,
                'type'   => Password::class,
            ],
            $args->target[$this->newField] ?? []
        );

        if (empty($args->id)) {
            $args->target[$this->newField]['formRules'] = [$this->web->formRuleRequired()];
        } else {
            $args->target[$this->newField]['tips'] = 'Do not fill if not need';
        }

        return $args->target;
    }

    /**
     * @param Arguments $args
     *
     * @return Error|array
     */
    public function tailorPersistenceAfterSubmit(Arguments $args)
    {
        $newPassword = Helper::dig($args->extraSubmit, $this->newField);

        if (isset($newPassword) && strlen($newPassword) > 0) {
            $result = $this->web->validator($this->newField, $newPassword, [$args->passwordValidator]);
            if ($result === false) {
                return new ErrorParameter($this->web->pop());
            }

            $salt = $args->target["{$this->fieldCamel}NewPasswordSalt"] ?? null;
            $args->extraSubmit[$this->fieldCamel] = $this->web->password($newPassword, $salt);
        }

        return [$args->target, $args->extraSubmit];
    }
}