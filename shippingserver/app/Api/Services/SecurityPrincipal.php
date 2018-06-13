<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 03-02-2017
 * Time: 19:11
 */

namespace Api\Services;


class SecurityPrincipal
{

    protected $userId;
    protected $userName;
    protected $email;
    protected $ipaddress;
    protected $expiresAt;
    protected $issuedAt;
    protected $issuedBy;
    protected $primaryRole;
    protected $currentRole;

    public function create($jws, $signingKey)
    {

        //TODO: Decrypt jws and get all values and set to the class variables
        //TODO: Create this SecurityPrincipal and store it in the Request object ($request->atributes->)

        return new SecurityPrincipal();
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return SecurityPrincipal
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     * @return SecurityPrincipal
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return SecurityPrincipal
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * @param mixed $ipaddress
     * @return SecurityPrincipal
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     * @return SecurityPrincipal
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @param mixed $issuedAt
     * @return SecurityPrincipal
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssuedBy()
    {
        return $this->issuedBy;
    }

    /**
     * @param mixed $issuedBy
     * @return SecurityPrincipal
     */
    public function setIssuedBy($issuedBy)
    {
        $this->issuedBy = $issuedBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryRole()
    {
        return $this->primaryRole;
    }

    /**
     * @param mixed $primaryRole
     * @return SecurityPrincipal
     */
    public function setPrimaryRole($primaryRole)
    {
        $this->primaryRole = $primaryRole;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentRole()
    {
        return $this->currentRole;
    }

    /**
     * @param mixed $currentRole
     * @return SecurityPrincipal
     */
    public function setCurrentRole($currentRole)
    {
        $this->currentRole = $currentRole;
        return $this;
    }


}