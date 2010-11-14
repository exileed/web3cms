<?php

namespace Application\SiteBundle\Model;

/**
 * The Contact Form
 */
class Contact
{
    /**
     * @Validation({
     *   @NotBlank,
     *   @MinLength(3)
     * })
     */
    public $content;

    /**
     * @Validation({
     *   @NotBlank,
     *   @Email
     * })
     */
    public $email;//(message="Please enter your email address.")

    /**
     * @Validation({
     *   @NotBlank
     * })
     */
    public $name;

    /**
     * @Validation({
     *   @NotBlank,
     *   @MinLength(2)
     * })
     */
    public $subject;

    //public $verifyCode;
}