<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 14:36
 */

namespace Api\Framework\Workflow;


abstract class AbstractStateMachine implements IStateMachine
{
    protected $states = [];

    protected $transitions = [];

    public function addState($stateName, $label, $type = "")
    {
        if (isset($this->states[$stateName])) {
            throw new \InvalidArgumentException(
                'State already exists'
            );
        }
        if ($type != "") {
            $state = new State($stateName, $label, $type);
        } else {
            $state = new State($stateName, $label);
        }
        $this->states[$stateName] = $state;

        return $this;
    }

    public function addTransition(Transition $transition)
    {

        if (isset($this->transitions[$transition->getName()])) {
            throw new \InvalidArgumentException(
                'Transition already exists'
            );
        }

        if (!$this->states[$transition->fromStateName()]) {
            throw new \InvalidArgumentException(
                'Invalid From State'
            );
        }

        if (!$this->states[$transition->toStateName()]) {
            throw new \InvalidArgumentException(
                'Invalid To State'
            );
        }

        $this->transitions[$transition->getName()] = $transition;

        return $this;
    }

    /**
     * Get all states under this state machine
     * @return mixed
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Get state under this state machine
     * @return mixed
     */
    public function getState($stateName)
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException(
                'Invalid State'
            );
        }
        return $this->states[$stateName];
    }

    /**
     * Gets the available transitions
     * @return mixed
     */
    public function getTransitions()
    {
        return $this->transitions;
    }


    public function getTransition($transitionName)
    {

        return $this->transitions[$transitionName];

    }


}