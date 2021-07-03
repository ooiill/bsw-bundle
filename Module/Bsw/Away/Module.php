<?php

namespace Leon\BswBundle\Module\Bsw\Away;

use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Controller\BswBackendController;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Bsw\ArgsInput;
use Leon\BswBundle\Module\Bsw\ArgsOutput;
use Leon\BswBundle\Module\Bsw\Bsw;
use Leon\BswBundle\Module\Scene\Message;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Exception\LogicException;
use Leon\BswBundle\Module\Exception\ModuleException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use BadFunctionCallException;

/**
 * @property Input                $input
 * @property BswBackendController $web
 */
class Module extends Bsw
{
    /**
     * @const string
     */
    const BEFORE_AWAY = 'BeforeAway';
    const AFTER_AWAY  = 'AfterAway';

    /**
     * @return string
     */
    public function name(): string
    {
        return 'away';
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
     * @throws
     */
    public function logic(): ArgsOutput
    {
        if (empty($this->entity)) {
            throw new ModuleException('Entity is required for away module');
        }

        if (empty($this->input->id)) {
            throw new ModuleException('Arguments `id` is required for away');
        }

        $result = $this->repository->transactional(
            function () {

                $effect = [];
                $pk = $this->repository->pk();

                /**
                 * Before away
                 */

                $arguments = $this->arguments(['id' => $this->input->id], $this->input->args);
                $effect[Abs::TAG_TRANS_BEFORE] = $result = $this->caller(
                    $this->method,
                    self::BEFORE_AWAY,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($result instanceof Error) {
                    throw new LogicException($result->tiny());
                }

                if (($result instanceof Message) && !$result->isSuccessClassify()) {
                    $message = $this->web->messageLang($result->getMessage(), $result->getArgs());
                    throw new LogicException($message);
                }

                /**
                 * Current entity
                 */

                $effect[$this->entity] = $result = $this->repository->away([$pk => $this->input->id]);
                if ($result === false) {
                    throw new RepositoryException($this->repository->pop());
                }

                /**
                 * Relation entity
                 */

                foreach ($this->input->relation as $entity => $relationId) {
                    $class = FoundationEntity::class;
                    if (!Helper::extendClass($entity, $class)) {
                        throw new BadFunctionCallException("Relation `entity` should be instance of `{$class}`");
                    }

                    $repository = $this->web->repo($entity);
                    $effect[$entity] = $result = $repository->away([$relationId => $this->input->id]);
                    if ($result === false) {
                        throw new RepositoryException($repository->pop());
                    }
                }

                /**
                 * After away
                 */

                $effect[Abs::TAG_TRANS_AFTER] = $result = $this->caller(
                    $this->method,
                    self::AFTER_AWAY,
                    [Message::class, Error::class, true],
                    null,
                    $arguments
                );

                if ($result instanceof Error) {
                    throw new LogicException($result->tiny());
                }

                if (($result instanceof Message) && !$result->isSuccessClassify()) {
                    $message = $this->web->messageLang($result->getMessage(), $result->getArgs());
                    throw new LogicException($message);
                }

                return $effect;
            }
        );

        /**
         * Handle error
         */
        if ($result === false) {
            return $this->showError($this->repository->pop());
        }

        $count = count($result) - 2;
        $type = $count > 1 ? 6 : 5;

        $relation = $this->input->relation;
        $relation[$this->entity] = $this->repository->pk();

        $this->web->databaseOperationLogger($this->entity, $type, $relation, $result, ['effect' => $count]);

        return $this->showSuccess(
            $this->input->i18nAway,
            $this->input->sets,
            $this->input->i18nArgs,
            isset($this->input->sets['function']) ? null : $this->input->nextRoute
        );
    }
}