<?php

namespace ApiV2\Framework\Workflow;
/**
 * Represents a State
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 13:56
 */
class State implements IState
{

    private $name;

    private $label;

    private $type;

    /**
     * State constructor.
     * @param bool $initial
     * @param bool $terminal
     * @param $name
     * @param $label
     */
    public function __construct($name, $label, $type = self::NORMAL_STATE)
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
    }

    public function isInitial()
    {
        return $this->type === self::INITIAL_STATE;
    }

    public function isFinal()
    {
        return $this->type === self::FINAL_STATE;
    }

    public function isNormal()
    {
        return $this->type === self::TYPE_NORMAL;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label;
    }
}