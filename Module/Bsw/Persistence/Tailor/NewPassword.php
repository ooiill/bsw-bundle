<?php

namespace Leon\BswBundle\Module\Bsw\Persistence\Tailor;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Bsw\Arguments;
use Leon\BswBundle\Module\Bsw\Tailor;
use Leon\BswBundle\Module\Error\Entity\ErrorParameter;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Form\Entity\Password;

class NewPassword extends Tailor
{
    /**
     * @var string
     */
    protected $newField = 'new_password';

    /**
     * @param Arguments $args
     *
     * @return array
     */
    public function tailorPersistenceAnnotation(Arguments $args): array
    {
        $sort = $args->persistAnnotation[$this->fieldCamel]['sort'] + .01;
        $args->target[$this->newField] = [
            'sort'   => $sort,
            'column' => 8,
            'type'   => Password::class,
        ];

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

            $salt = $args->target["{$this->field}Salt"] ?? null;
            $args->extraSubmit[$this->fieldCamel] = $this->web->password($newPassword, $salt);
        }

        return [$args->target, $args->extraSubmit];
    }
}