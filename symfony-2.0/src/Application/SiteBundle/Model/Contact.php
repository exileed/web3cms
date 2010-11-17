<?php

namespace Application\SiteBundle\Model;

//use Symfony\Component\Validator\Constraints;
//use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Site Contact Form
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

    /*public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('content', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('content', new Constraints\MinLength(3));
        $metadata->addPropertyConstraint('email', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('email', new Constraints\Email(array(
            //'message' => 'Please enter your email address.',
        )));
        $metadata->addPropertyConstraint('name', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('subject', new Constraints\NotBlank());
        $metadata->addPropertyConstraint('subject', new Constraints\MinLength(2));
    }*/
}