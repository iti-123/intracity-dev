<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 14:01
 */

namespace ApiV2\Framework\Workflow;

/**
 * Models a State Machine
 * @package Api\Framework\Workflow
 */
interface IStateMachine
{

    /**
     * Get all states under this state machine
     * @return mixed
     */
    public function getStates();

    /**
     * Gets the available transitions
     * @return mixed
     */
    public function getTransitions();

    /**
     * The name of this state machine
     * @return mixed
     */
    public function getName();

}