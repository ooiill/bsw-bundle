<?php

namespace Leon\BswBundle\Module\Traits;

trait Variable
{
    /**
     * @var array
     */
    protected $variable = [];

    /**
     * @var string
     */
    protected $variableKeySplit = '.';

    /**
     * @param string $variableKeySplit
     *
     * @return $this
     */
    public function setVariableKeySplit(string $variableKeySplit)
    {
        $this->variableKeySplit = $variableKeySplit;

        return $this;
    }

    /**
     * Get variable
     *
     * @param string $key
     *
     * @return mixed
     * @throws
     */
    public function getVariable(string $key)
    {
        $keys = explode($this->variableKeySplit, $key);
        $len = count($keys);

        $var = $this->variable;
        for ($i = 0; $i < $len; $i++) {
            $k = $keys[$i];
            if (!isset($keys[$i + 1])) {
                $var = $var[$k] ?? null;
                break;
            }
            $var = $var[$k] ?? [];
        }

        return $var;
    }

    /**
     * Set variable
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     * @throws
     */
    public function setVariable(string $key, $value = null)
    {
        $keys = explode($this->variableKeySplit, $key);
        $len = count($keys);
        $var = &$this->variable;

        for ($i = 0; $i < $len; $i++) {
            $k = $keys[$i];
            if (!isset($keys[$i + 1])) {
                $var[$k] = $value;
                break;
            }

            if (!isset($var[$k])) {
                $var[$k] = [];
                $var = &$var[$k];
                continue;
            }

            if (!is_array($var[$k])) {
                return false;
            }

            $var = &$var[$k];
        }

        return $this->variable;
    }
}