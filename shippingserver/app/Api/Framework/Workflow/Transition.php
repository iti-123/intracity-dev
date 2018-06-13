<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 13:59
 */

namespace Api\Framework\Workflow;

use Api\BusinessObjects\OrderBO;


/**
 * A Transition
 * @package Api\Framework\Workflow
 */
abstract class Transition
{

    /**
     * Name of the transition
     * @var
     */
    protected $name;

    /**
     * From State
     * @var
     */
    protected $fromState;

    /**
     * TO State
     * @var string
     */
    protected $toState;


    public function __construct($name, State $fromState, State $toState)
    {
        $this->name = $name;
        $this->fromState = $fromState;
        $this->toState = $toState;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function fromState()
    {
        return $this->fromState;
    }

    /**
     * @return mixed
     */
    public function fromStateName()
    {
        return $this->fromState->getName();
    }

    /**
     * @return mixed
     */
    public function toState()
    {
        return $this->toState;
    }

    /**
     * @return mixed
     */
    public function toStateName()
    {
        return $this->toState->getName();
    }

    /**
     * Apply this transition.
     * @param Transitionable $transitionable
     * @return mixed
     */
    abstract public function apply($trasition, OrderBO $bo, $request);

    /**
     * Returns if this transitionable is ready for applying this transition
     * @param Transitionable $transitionable
     * @return mixed
     */
    abstract public function isReady($trasition, OrderBO $bo);


}