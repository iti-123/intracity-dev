<?php

namespace ApiV2\Framework;

interface ICartItemAuthorizer
{
    function authorizeGet();

    function authorizeSave();

    function authorizeDelete();

}