<?php

namespace Application\UserBundle\Model;

/**
 * User Login Form
 * In future allow to login with username or email or any of these.
 */
class Login
{
    public $email;

    /**
     * @Validation({
     *   @NotBlank
     * })
     */
    public $password;

    public $rememberMe;

    /**
     * @Validation({
     *   @NotBlank
     * })
     */
    public $username;

    public $usernameOrEmail;
}